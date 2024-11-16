




document.getElementById("out").addEventListener("click", function () {
    var loginDiv = document.querySelector(".out");
    loginDiv.style.display = (loginDiv.style.display === "none" || loginDiv.style.display === "") ? "block" : "none";
    document.getElementById("tex").innerHTML = "";
    var loginDiv = document.querySelector(".login");
    loginDiv.style.display = "none";
});
document.getElementById("inter").addEventListener("click", function () {
    var loginDiv = document.querySelector(".login");
    loginDiv.style.display = (loginDiv.style.display === "none" || loginDiv.style.display === "") ? "block" : "none";
    document.getElementById("tex").innerHTML = "";
    var loginDiv = document.querySelector(".out");
    loginDiv.style.display = "none";

});

document.getElementById("back_inter").addEventListener("click", function () {
    var loginDiv = document.querySelector(".login");
    loginDiv.style.display = "none";
});

document.getElementById("back_out").addEventListener("click", function () {
    var loginDiv = document.querySelector(".out");
    loginDiv.style.display = "none";
});








      document.addEventListener('DOMContentLoaded', function () {
        var facultySelect = document.getElementById('facultySelect');
        var typeSelect = document.getElementById('Select'); // Corrected the ID here
        var mapContainer = document.getElementById('map');
        var carEC = document.getElementById('carEC');
        var motorEC = document.getElementById('motorEC');
        var carFTI = document.getElementById('carFTI');
        var motorFTI = document.getElementById('motorFTI');
        var motorFaculty_Business_Economics = document.getElementById('motorFaculty of Business & Economics');
        var carFaculty_Business_Economics = document.getElementById('carFaculty of Business & Economics');
        var carFaculty_Law = document.getElementById('carFaculty of Law');
        var motorFaculty_Law = document.getElementById('motorFaculty of Law');
        var carFaculty_Islamic_Religious_Sciences = document.getElementById('carFaculty of Islamic Religious Sciences');
        var motorFaculty_Islamic_Religious_Sciences = document.getElementById('motorFaculty of Islamic Religious Sciences');
        var carFaculty_Medicine_Law = document.getElementById('carFaculty of Medicine Law');
        var motorFaculty_Medicine_Law = document.getElementById('motorFaculty of Medicine Law');

        function updateMap() {
          var facultyValue = facultySelect.value;
          var typeValue = typeSelect.value;

          // Hide all maps
          carEC.style.display = 'none';
          motorEC.style.display = 'none';
          carFTI.style.display = 'none';
          motorFTI.style.display = 'none';
          carFaculty_Business_Economics.style.display = 'none';
          motorFaculty_Business_Economics.style.display = 'none';
          carFaculty_Law.style.display = 'none';
          motorFaculty_Law.style.display = 'none';
          carFaculty_Islamic_Religious_Sciences.style.display = 'none';
          motorFaculty_Islamic_Religious_Sciences.style.display = 'none';
          carFaculty_Medicine_Law.style.display = 'none';
          motorFaculty_Medicine_Law.style.display = 'none';

          // Show the selected map based on faculty and type
          if (facultyValue === 'option1') {
                if (typeValue === 'option1') {
                    carEC.style.display = 'block';
                } else if (typeValue === 'option2') {
                    motorEC.style.display = 'block';
                }
            } else if (facultyValue === 'option2') {
                if (typeValue === 'option1') {
                    carFTI.style.display = 'block';
                } else if (typeValue === 'option2') {
                    motorFTI.style.display = 'block';
                }
            } else if (facultyValue === 'option3') {
                if (typeValue === 'option1') {
                    carFaculty_Business_Economics.style.display = 'block';
                } else if (typeValue === 'option2') {
                    motorFaculty_Business_Economics.style.display = 'block';
                }
            } else if (facultyValue === 'option4') {
                if (typeValue === 'option1') {
                    carFaculty_Law.style.display = 'block';
                } else if (typeValue === 'option2') {
                    motorFaculty_Law.style.display = 'block';
                }} else if (facultyValue === 'option5') {
            if (typeValue === 'option1') {
              carFaculty_Islamic_Religious_Sciences.style.display = 'block';
            } else if (typeValue === 'option2') {
              motorFaculty_Islamic_Religious_Sciences.style.display = 'block';
            }
          } else if (facultyValue === 'option6') {
            if (typeValue === 'option1') {
              carFaculty_Medicine_Law.style.display = 'block';
            } else if (typeValue === 'option2') {
              motorFaculty_Medicine_Law.style.display = 'block';
            }
          }
        }

        // Attach the updateMap function to the change event of the selects
        facultySelect.addEventListener('change', updateMap);
        typeSelect.addEventListener('change', updateMap);

        // Initial map update
        updateMap();
      });
  

      function onScanSuccess(qrCodeMessage) {
        // Set the value of the input field to the scanned barcode
        document.getElementById('input').value = qrCodeMessage;
    
        // Display the result in the result div
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
    
    // Attach the scanner to the "Scan Barcode" button
    document.getElementById('scanBarcodeBtn').addEventListener('click', function () {
        document.getElementById('scanner-container').style.display = 'block';
        document.getElementById('scanResult').style.display = 'none';
        html5QrcodeScanner.render(onScanSuccess, onScanError);
    });
    
    // Add logic to hide/show relevant elements when scanning is complete
    html5QrcodeScanner.onScanComplete = function () {
        document.getElementById('scanner-container').style.display = 'none';
        document.getElementById('scanResult').style.display = 'block';
    };
    
    function scrollToTop() {
        document.body.scrollTop = 0; // لمتصفحات قديمة
        document.documentElement.scrollTop = 0; // لمتصفحات حديثة
    }
    
    // عرض أو إخفاء الزر عند التمرير
    window.onscroll = function () { scrollFunction() };
    
    function scrollFunction() {
        var scrollToTopBtn = document.getElementById("scrollToTopBtn");
    
        // إذا كان المتصفح يتم التمرير إلى أسفل، أظهر الزر
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            scrollToTopBtn.style.display = "block";
        } else {
            // إذا كان المتصفح في أعلى الصفحة، أخفِ الزر
            scrollToTopBtn.style.display = "none";
        }
    }



document
.getElementById("areaSelect")
.addEventListener("change", function () {
  var selectedArea = this.value;

  // AJAX request to fetch data from PHP backend
  var xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      var responseData = JSON.parse(this.responseText);
      console.log(responseData); // Log the received data for debugging
      createChart(responseData);
    }
  };
  xhr.open("GET", "get_parking_data.php?area_id=" + selectedArea, true);
  xhr.send();
});
// Create Chart Function
function createChart(data) {
var ctx = document.getElementById("myChart").getContext("2d");
var myChart = new Chart(ctx, {
  type: "pie",
  data: {
    labels: ["Available Spaces", "Unavailable Spaces"],
    datasets: [
      {
        label: "Parking Area Statistics",
        data: [data.available_spaces, data.unavailable_spaces],
        backgroundColor: [
          "rgba(54, 162, 235, 0.5)", // Available Spaces
          "rgba(255, 99, 132, 0.5)", // Unavailable Spaces
        ],
        borderColor: ["rgba(54, 162, 235, 1)", "rgba(255, 99, 132, 1)"],
        borderWidth: 1,
      },
    ],
  },
});
}


