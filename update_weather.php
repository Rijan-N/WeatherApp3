<?php

// Create a connection to the MySQL database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "weathers";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set the city for the API request
$city = "South Gloucestershire";

// Get the API key
$apiKey = "9f23b56e8dcad8299bf4e5a2a3fc932b";

// Get the weather data from the API
$url = "http://api.openweathermap.org/data/2.5/weather?q=" . urlencode($city) . "&units=metric&appid=" . $apiKey;

// Create a new cURL resource
$curl = curl_init();

// Set the cURL options
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

// Execute the cURL request
$response = curl_exec($curl);

// Close the cURL resource
curl_close($curl);

// Check if the API request was successful
if ($response !== false) {
    // Decode the JSON response
    $data = json_decode($response, true);

    // Check if the data contains the required information
    if (isset($data['weather'][0]['description']) && isset($data['main']['temp']) && isset($data['wind']['speed']) && isset($data['main']['humidity'])) {
        // Extract the weather information
        $description = $data['weather'][0]['description'];
        $temperature = $data['main']['temp'];
        $wind = $data['wind']['speed'];
        $humidity = $data['main']['humidity'];
        $icon = $data['weather'][0]['icon'];
        $dt = date('Y-m-d H:i:s');

        // Check if an entry already exists for the current date
        $checkQuery = "SELECT * FROM weatherstable WHERE DATE(dt) = CURDATE()";
        $checkResult = $conn->query($checkQuery);

        if ($checkResult->num_rows === 0) {
            // Prepare the SQL statement to insert the data into the database
            $sql = "INSERT INTO weatherstable (description, temperature, wind, city, humidity, icon, dt) 
                    VALUES ('$description', '$temperature', '$wind', '$city', '$humidity', '$icon', '$dt')";

            // Execute the SQL statement
            if ($conn->query($sql) === TRUE) {
                // Notify the user that the data has been stored in the database
                echo "Weather data for South Gloucestershire has been stored in the database.";
            } else {
                echo "Error storing weather data in the database. Error: " . $conn->error;
            }
        } else {
            echo "An entry already exists for the current date. Skipping insertion.";
        }

        // Delete weather data older than 7 days
        $deleteQuery = "DELETE FROM weatherstable WHERE dt < DATE_SUB(NOW(), INTERVAL 7 DAY)";
        if ($conn->query($deleteQuery) === TRUE) {
            echo "Weather data older than 7 days has been deleted.";
        } else {
            echo "Error deleting weather data: " . $conn->error;
        }
    }
}
?>
