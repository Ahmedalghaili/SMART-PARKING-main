<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "puiii";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// Assume $conn is a valid database connection

if (isset($_POST['EnterParking'])) {
    $faculty_id = $_POST["faculty_id"];

    $area_id_query = "SELECT area_id, available_spaces, total_spaces, unavailable_spaces FROM parkingareas WHERE faculty_id = ?";
    $stmt_area = $conn->prepare($area_id_query);
    $stmt_area->bind_param("i", $faculty_id);
    $stmt_area->execute();
    $result_area = $stmt_area->get_result();

    if ($result_area->num_rows > 0) {
        $row_area = $result_area->fetch_assoc();
        $area_id = $row_area["area_id"];
        $available_spaces = $row_area["available_spaces"];
        $total_spaces = $row_area["total_spaces"];
        $unavailable_spaces = $row_area["unavailable_spaces"];

        if ($available_spaces > 0 && $available_spaces <= $total_spaces && $unavailable_spaces >= 0 && $unavailable_spaces < $total_spaces) {
            $conn->begin_transaction();

            $updateSpaces = "UPDATE parkingareas SET available_spaces = ?, unavailable_spaces = ? WHERE area_id = ?";
            $stmt_update = $conn->prepare($updateSpaces);
            $newAvailableSpaces = $available_spaces - 1;
            $newUnavailableSpaces = $unavailable_spaces + 1;
            $stmt_update->bind_param("iii", $newAvailableSpaces, $newUnavailableSpaces, $area_id);

            if ($stmt_update->execute()) {
                $sql_insert = "INSERT INTO parkingtransactions (area_id, entry_time) VALUES (?, NOW())";
                $stmt_insert = $conn->prepare($sql_insert);
                $stmt_insert->bind_param("i", $area_id);

                if ($stmt_insert->execute()) {
                    $transaction_id = $stmt_insert->insert_id;
                    echo '<div id="transaction_id_display">' . $transaction_id . '</div>';
                    $conn->commit();
                } else {
                    $message = "Error adding transaction: " . $stmt_insert->error;
                    $conn->rollback();
                }
            } else {
                $message = "Error updating parking area: " . $stmt_update->error;
                $conn->rollback();
            }
        } else {
            $message = "Parking area is full or invalid. Cannot enter.";
        }
    } else {
        $message = "No corresponding area ID found for selected faculty.";
    }
}


if (isset($_POST['exit_parking'])) {
    $transaction_id_out = $_POST["transaction_id"];

    $checkTransaction = "SELECT * FROM parkingtransactions WHERE transaction_id = '$transaction_id_out'";
    $result = $conn->query($checkTransaction);

    if ($result->num_rows > 0) {
        $updateExitTime = "UPDATE parkingtransactions 
                           SET exit_time = NOW() 
                           WHERE transaction_id = '$transaction_id_out'";

        if ($conn->query($updateExitTime) === TRUE) {
            $area_id_query = "SELECT area_id FROM parkingtransactions WHERE transaction_id = '$transaction_id_out'";
            $area_result = $conn->query($area_id_query);
            $row = $area_result->fetch_assoc();
            $area_id = $row['area_id'];

            $updateSpaces = "UPDATE parkingareas 
                             SET available_spaces = LEAST(available_spaces + 1, total_spaces),
                                 unavailable_spaces = GREATEST(unavailable_spaces - 1, 0)
                             WHERE area_id = $area_id";

            if ($conn->query($updateSpaces) === TRUE) {
                // Delete the transaction entry after successful exit recording
                $deleteTransaction = "DELETE FROM parkingtransactions WHERE transaction_id = '$transaction_id_out'";
                if ($conn->query($deleteTransaction) === TRUE) {
                    echo "Exit recorded successfully and transaction deleted.";
                } else {
                    echo "Error deleting transaction: " . $conn->error;
                }
            } else {
                echo "Error updating spaces: " . $conn->error;
            }
        } else {
            echo "Error updating exit time: " . $conn->error;
        }
    } else {
        echo "Transaction ID not found. Please enter a valid ID.";
    }
}

$conn->close();
?>

<?php echo $message; ?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Smart Parking</title>
    <!-- Style Link -->
    <link rel="stylesheet" href="style.css">
    <!-- Fontawesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css"
        integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="./favicon.ico" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <script src="html5-qrcode.min.js"></script>
    <script src="https://cdn.rawgit.com/serratus/quaggaJS/0.12.1/dist/quagga.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous">
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



</head>

<body>
    <!-- Header Start -->
    <header>
        <nav class="navbar navbar-expand-sm navbar-light bg-primary">
            <div class="container-fluid">
                <!-- <a class="navbar-brand " href="#">Navbar</a> -->
                <img class="navbar-brand" src="./img/logo parking 1.png" alt="Parking"
                    style="height: 60px; width: 120px" />
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link active text-light" aria-current="page" href="#navbar">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active text-light" aria-current="page" href="#find-location">Available
                                Spaces</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-light" aria-current="page" href="#about">About Us</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active text-light" aria-current="page" href="#offers">Location
                                Parking</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active text-light" aria-current="page" href="#contact">Contact Us</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="content">
            <div>
                <h1>Welcome To <span class="primary-text"> Parking </span> UII</h1>
            </div>
            <p>Here you can enter the parking or exit</p>
            <div class="btn-input" id="btn">
                <button id="inter" class="btn btn-primary">Entrance</button>
                <button id="out" class="btn btn-primary">Out</button>
            </div>


            <div class="login">
                <form method="post" action="">
                    <h1 id="label">Enter Parking</h1><br />
                    <input id="input" type="text" name="area_id" />
                    <h1 id="tex"></h1>
                    <div id="id"></div>
                    <select id="faculty_vehicle" name="faculty_id">
                        <option value="1">Faculty of Business & Economics C</option>
                        <option value="2">Faculty of Business & Economics M</option>
                        <option value="3">Faculty of Law C</option>
                        <option value="5">Faculty of Law M</option>
                        <option value="7">Faculty of Islamic Religious Sciences M</option>
                        <option value="8">Faculty of Islamic Religious Sciences C</option>
                        <option value="4">Faculty of Medicine, M</option>
                        <option value="9">Faculty of Medicine C</option>
                        <option value="10">Faculty of Mathematics and Natural Sciences M</option>
                        <option value="11">Faculty of Mathematics and Natural Sciences C</option>
                        <option value="6">Faculty of Psychology and Social and Cultural Sciences C</option>
                        <option value="12">Faculty of Psychology and Social and Cultural Sciences M</option>
                        <option value="13">Faculty of Civil Engineering and Planning C</option>
                        <option value="14">Faculty of Civil Engineering and Planning M</option>
                        <option value="15">Faculty of Industrial Technology M</option>
                        <option value="16">Faculty of Industrial Technology C</option>
                        <!-- Other options... -->
                    </select>
                    <button id="back_inter" class="back">Back</button>

                    <input type="submit" name="EnterParking" class="submit" value="Enter Parking" />

                    <div id="scanner-container" style="display: none">
                        <div style="width: 300px; height: 200px; display: flex" id="reader"></div>
                    </div>
                </form>

                <div id="scanResult" style="display: none">
                    <h4>SCAN RESULT</h4>
                    <div id="result">Result Here</div>
                </div>
                <button id="scanBarcodeBtn" class="btn btn-primary">
                    Scan Barcode
                </button>
            </div>

            <script>
            function onScanSuccess(qrCodeMessage) {
                document.getElementById('input').value = qrCodeMessage;
                document.getElementById('result').innerHTML = '<span class="result">' + qrCodeMessage + '</span>';
            }

            function onScanError(errorMessage) {
                // handle scan error
            }

            var html5QrcodeScanner = new Html5QrcodeScanner(
                "reader", {
                    fps: 10,
                    qrbox: 250
                });

            document.getElementById('scanBarcodeBtn').addEventListener('click', function() {
                document.getElementById('scanner-container').style.display = 'block';
                document.getElementById('scanResult').style.display = 'none';
                html5QrcodeScanner.render(onScanSuccess, onScanError);
            });

            html5QrcodeScanner.onScanComplete = function() {
                document.getElementById('scanner-container').style.display = 'none';
                document.getElementById('scanResult').style.display = 'block';
            };

            function scrollToTop() {
                document.body.scrollTop = 0;
                document.documentElement.scrollTop = 0;
            }

            window.onscroll = function() {
                scrollFunction()
            };

            function scrollFunction() {
                var scrollToTopBtn = document.getElementById("scrollToTopBtn");

                if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                    scrollToTopBtn.style.display = "block";
                } else {
                    scrollToTopBtn.style.display = "none";
                }
            }
            </script>





            <div class="out">
                <form method="post" action="">
                    <h1 id="label">Exit Parking</h1><br>
                    Transaction ID:
                    <input id="input" type="text" name="transaction_id" />

                    <input type="submit" name="exit_parking" class="submit">Exit Parking</input>
                    <button id="back_out" class="back">Back</button>

                </form>


            </div>
            <div id="transaction_id_display">
                <h1><?php echo isset($transaction_id) ? "Your id is: " . $transaction_id : ''; ?></h1>
            </div>


        </div>
    </header>

    <find id="find-location">
        <div>
            <div class="title1">
                <h1 style="margin-top: -262px ;color : #e4b95b">Available Spaces on parker</h1>
            </div>

            <!-- <div style="float: left; margin: 43px"> -->
            <div style="float: left; margin: 43px">

                <form id="statsForm" method="post" action="fetch_stats.php" style="display: flex">
                    <div class="select">
                        <label for="faculty_id">Select Faculty:</label>
                        <select id="faculty_id" name="faculty_id">
                            <option value="1">Faculty of Business & Economics</option>
                            <option value="2">Faculty of Law</option>
                            <option value="3">Faculty of Islamic Religious Sciences</option>
                            <option value="4">Faculty of Medicine</option>
                            <option value="5">Faculty of Mathematics and Natural Sciences</option>
                            <option value="6">Faculty of Psychology and Social and Cultural Sciences</option>
                            <option value="7">Faculty of Civil Engineering and Planning</option>
                            <option value="8">Faculty of Industrial Technology</option>
                            <!-- Other options... -->
                        </select>
                    </div>

                    <div>
                        <label for="area_type">Select Area Type:</label>
                        <select id="area_type" name="area_type">
                            <option value="Car">Car</option>
                            <option value="Motorcycle">Motorcycle</option>
                        </select>
                    </div>
                    <input type="submit" value="Display Stats" />
                </form>
                <div class="available-spaces" style="background-color: blanchedalmond">
                    Available Spaces: <span id="availableSpacesCount">0</span>
                </div>
            </div>

            <div class="char">
                <canvas class="char1" id="pieChart"></canvas>
            </div>
        </div>

        <script>
        document
            .getElementById("statsForm")
            .addEventListener("submit", function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                fetch("fetch_stats.php", {
                        method: "POST",
                        body: formData,
                    })
                    .then((response) => response.json())
                    .then((data) => {
                        var unavailableSpaces = data.unavailable_spaces;
                        var availableSpaces = data.available_spaces;

                        document.getElementById("availableSpacesCount").textContent =
                            availableSpaces;

                        var ctx = document.getElementById("pieChart").getContext("2d");
                        var myChart = new Chart(ctx, {
                            type: "pie",
                            data: {
                                labels: ["Unavailable Spaces", "Available Spaces"],
                                datasets: [{
                                    data: [unavailableSpaces, availableSpaces],
                                    backgroundColor: [
                                        "rgba(255, 99, 132, 0.8)",
                                        "rgba(54, 162, 235, 0.8)",
                                    ],
                                    borderWidth: 1,
                                }, ],
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                // Other chart options can be added here as needed
                            },
                        });
                    })
                    .catch((error) => console.error("Error:", error));
            });
        </script>
    </find>

    <!-- Header End -->
    <main>
        <!-- About Section Start -->
        <section id="about">
            <div class="container">
                <div class="title">
                    <h2>About us</h2>
                    <p>
                        Hello, we are from CPT team we can help you to make the best
                        website
                    </p>
                </div>
                <div class="card mb-3" style="max-width: 100%">
                    <div class="row g-0 bg-blue-800">
                        <div class="col-6 col-md-5">
                            <img src="./img/iot.jpeg" style="width: 200%" class="card-img" alt="..." />
                        </div>
                        <div class="col-md-7">
                            <div class="card-body">
                                <h5 class="card-title text-light">Card title</h5>
                                <p class="card-text text-light">
                                    Tihis website is being implemented to solve the problems and
                                    challenges of parking cars and motorcycles. Increasing the
                                    number of cars and motorcycles on the campus makes it leads
                                    to problem of Crowd, stopping vehicles, and difficulties in
                                    finding places to vehicles. This project aims to benefit
                                    from smart technology in parking to make it easier for users
                                    and lessen the problem of crowds
                                </p>
                                <!-- <p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p> -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text1" style="text-align: center;">
                    <h1>Why Choose US</h1>
                </div>
                <div class="row gy-3">
                    <div class="col-md-4">
                        <div class="card h-10 bg-info" style="width: 18rem;height: 90%;">
                            <img src="./img/Rectangle 33.png" class="card-img-top-img" alt="...">
                            <div class="card-body">
                                <!-- <h5 class="card-title">Card title</h5> -->
                                <p class="card-text text-light">Enhance your overall productivity and streamline daily
                                    tasks by optimizing and efficiently managing your time with our innovative solutions
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-19 bg-info" style="width: 18rem;height: 90%;">
                            <img src="./img/Rectangle 34.png" class="card-img-top-img" alt="...">
                            <div class="card-body">
                                <!-- <h5 class="card-title">Card title</h5> -->
                                <p class="card-text text-light">Optimize your peace of mind by leveraging our advanced
                                    smart parking system on campus, eliminating the hassle and stress associated with
                                    finding a parking spot effortlessly and efficiently.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-10 bg-info" style="width: 18rem;height: 90%; ">
                            <img src="./img/Rectangle 35.png" class="card-img-top-img" alt="...">
                            <div class="card-body">
                                <!-- <h5 class="card-title">Card title</h5> -->
                                <p class="card-text text-light">The system maximizes the use of parking spaces by
                                    efficiently allocating them based on real-time demand, preventing unnecessary</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>
        <!-- About Section End -->
        <!-- Offers Section Start -->



        <section id="offers">
            <div class="container">
                <div class="title">
                    <h2>Find location parking</h2>
                </div>
                <h1>Select Faculty</h1>
                <div class="group-map">
                    <div class="selectmap">
                        <select name="faculty" id="facultySelect" class="select-1">
                            <option value="option1">FTI</option>
                            <option value="option2">EC</option>
                            <option value="option3">Faculty of Business & Economics</option>
                            <option value="option4">Faculty of Law</option>
                            <option value="option5">Faculty of Islamic Religious Sciences</option>
                            <option value="option6">Faculty of Medicine Law</option>
                        </select>
                    </div>
                    <div class="selectpark">
                        <select name="type" id="Select" class="select-1">
                            <option value="option1">Car</option>
                            <option value="option2">Motorcycles</option>
                        </select>
                    </div>
                </div>



                <div class="rectangle">
                    <div class="map" id="map">
                        <div id="motorEC">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63256.71737156725!2d110.36427584375713!3d-7.731883325226925!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a5e970cd4fa51%3A0xa527d07122b90c40!2sUniversitas%20Islam%20Indonesia!5e0!3m2!1sen!2sid!4v1700581363610!5m2!1sen!2sid"
                                allowfullscreen="" loading="lazy" class="map" ;
                                referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                        <div id="carEC">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d9509.899470998795!2d110.41187198056531!3d-7.68460157077684!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a5e98e56c543d%3A0xbd93742d6ae5937c!2sFaculty%20of%20Industrial%20Technology%20UII!5e0!3m2!1sen!2sid!4v1701242014830!5m2!1sen!2sid"
                                allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                        <div id="carFTI">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d4094.2494519311363!2d110.41321671793608!3d-7.687008946997237!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a5fe016d36d5d%3A0xc48a6367f348cc35!2sFaculty%20of%20Law%20UII!5e0!3m2!1sen!2sid!4v1701242049949!5m2!1sen!2sid"
                                allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                        <div id="motorFTI">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d2305.8592214816613!2d110.4120598553111!3d-7.686516093137131!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a5e98f1ed9d0b%3A0x83e431a02e11deec!2sFaculty%20of%20Civil%20Engineering%20and%20Planning%20UII!5e0!3m2!1sen!2sid!4v1701242134503!5m2!1sen!2sid"
                                allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                        <div id="carFaculty of Business & Economics">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d472.2412618253504!2d110.41185587824059!3d-7.760252250895491!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a599a89888fd7%3A0x8459a7e7d3785766!2sGedung%20Pascasarjana%20Fakultas%20Ekonomi%20UII!5e1!3m2!1sar!2sid!4v1701750875817!5m2!1sar!2sid"
                                allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                        <div id="motorFaculty of Business & Economics">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63252.427741016036!2d110.459774035488!3d-7.760447987091277!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a599a55025691%3A0x2a33ac88db4969d7!2sFaculty%20of%20Business%20and%20Economics%20UII!5e0!3m2!1sar!2sid!4v1701750646602!5m2!1sar!2sid"
                                allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>


                        <div id="carFaculty of Law">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d718.4610045137715!2d110.41350531279689!3d-7.689760108945332!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a5fb0780c682d%3A0x897eb0ab9383374c!2sGedung%20Pengelola%20Fasilitas%20Kampus%20YBW%20UII!5e1!3m2!1sar!2sid!4v1701751047095!5m2!1sar!2sid"
                                allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>



                        <div id="motorFaculty of Law"> <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d718.4610045137715!2d110.41350531279689!3d-7.689760108945332!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a5fb0780c682d%3A0x897eb0ab9383374c!2sGedung%20Pengelola%20Fasilitas%20Kampus%20YBW%20UII!5e1!3m2!1sar!2sid!4v1701751047095!5m2!1sar!2sid"
                                allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                        <!-- Include your new map divs here -->
                        <div id="carFaculty of Islamic Religious Sciences">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d247.12211409886123!2d110.41493808333739!3d-7.688104699576185!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a5f9c9cb79d77%3A0x5ca86b2a718fb47f!2sProgram%20Studi%20Pendidikan%20Agama%20Islam!5e0!3m2!1sar!2sid!4v1701750500539!5m2!1sar!2sid"
                                allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                        <div id="motorFaculty of Islamic Religious Sciences">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d247.12211409886123!2d110.41493808333739!3d-7.688104699576185!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a5f9c9cb79d77%3A0x5ca86b2a718fb47f!2sProgram%20Studi%20Pendidikan%20Agama%20Islam!5e0!3m2!1sar!2sid!4v1701750500539!5m2!1sar!2sid"
                                allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                        <div id="carFaculty of Medicine Law"> <iframe
                                src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d988.4853271570606!2d110.413703065351!3d-7.689448199999981!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zN8KwNDEnMjIuNSJTIDExMMKwMjQnNDYuMiJF!5e0!3m2!1sar!2sid!4v1701750298253!5m2!1sar!2sid"
                                allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                        <div id="motorFaculty of Medicine Law"><iframe
                                src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d988.4853271570606!2d110.413703065351!3d-7.689448199999981!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zN8KwNDEnMjIuNSJTIDExMMKwMjQnNDYuMiJF!5e0!3m2!1sar!2sid!4v1701750298253!5m2!1sar!2sid"
                                allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                        <!-- ... (other map divs) ... -->

                    </div>
                </div>
        </section>





        <section id="contact">
            <div class="container">
                <div class="contact-content">
                    <div class="contact-info">
                        <div>
                            <h3>ADDRESS</h3>
                            <p>
                                <i class="fa-solid fa-location-dot"></i> Yogyakarta, 6
                                October, Indonesia
                            </p>
                            <p><i class="fa-solid fa-phone"></i> Phone: 123456789</p>
                            <p><i class="fa-regular fa-envelope"></i> support@uii.com</p>
                        </div>
                        <div>
                            <h3>WORKING HOURS</h3>
                            <p>8:00 am to 11:00 pm on Weekdays</p>
                            <p>11:00 am to 1:00 Am on Weekends</p>
                        </div>
                        <div>
                            <h3>FOLLOW US</h3>
                            <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                            <a href="#"><i class="fa-brands fa-twitter"></i></a>
                            <a href="#"><i class="fa-brands fa-instagram"></i></a>
                        </div>
                    </div>
                    <form method="post" action="mase.php">
                        <input type="text" name="Name" id="name" placeholder="Full Name" />
                        <input type="email" name="email" id="email" placeholder="Email Address" />
                        <input type="text" name="subject" id="subject" placeholder="Subject" />
                        <textarea name="message" id="message" cols="30" rows="5" placeholder="Message"></textarea>
                        <button type="submit" class="btn btn-third" style="
    background-color: white;
    border: 3px solid #e4b95b;
    border-radius: 10px;
    /* margin: 10px; */
    width: 130px;
    height: 50px;
    text-align: center;
    float: right;
    
    border-radius: 20px; /* Modified the syntax for border-radius */
">SEND </button>

                        <!-- <button type="submit" class="btn btn-third" action="retrieve_messages.php">show massege</button> -->
                    </form>
                </div>
            </div>
        </section>
    </main>

    <button id="scrollToTopBtn" onclick="scrollToTop()">
        <img src="img/car.jpg" alt="error" height="50px" width="50px" />
    </button>

    <footer id="footer">
        <p>
            Copyright &copy; 2023 All rights reserved | made by
            <b>
                <a href="https://www.uii.ac.id/" target="_blank"> CPT </a>
            </b>
        </p>
    </footer>
    <script src="script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>
</body>

</html>