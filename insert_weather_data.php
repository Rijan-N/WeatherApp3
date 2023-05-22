<?php

// Set the default timezone to Nepal Standard Time
date_default_timezone_set('Asia/Kathmandu');

// Create a connection to the MySQL database
$servername = "sql209.epizy.com";
$username = "epiz_34167501";
$password = "vouzm8YwZWroW";
$dbname = "epiz_34167501_weathers";

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
        $temperatureFahrenheit = $data['main']['temp'];
        $wind = $data['wind']['speed'];
        $humidity = $data['main']['humidity'];
        $icon = $data['weather'][0]['icon'];
        $dt = date('Y-m-d H:i:s');

        // Convert temperature from Fahrenheit to Celsius
        $temperatureCelsius = round(($temperatureFahrenheit - 32) * 5/9, 2);

        // Prepare the SQL statement to insert the data into the database
        $sql = "INSERT INTO weatherstable (description, temperature, wind, city, humidity, icon, dt) 
                VALUES ('$description', '$temperatureCelsius', '$wind', '$city', '$humidity', '$icon', '$dt')";

        // Execute the SQL statement
        if ($conn->query($sql) === TRUE) {
            // Notify the separate server or hosting environment that the data has been stored in the database on InfinityFree
            echo "success";
        } else {
            echo "error";
        }
    } else {
        echo "Unable to retrieve weather data for South Gloucestershire.";
    }
} else {
    echo "API request to OpenWeatherMap failed.";
}

// Close the database connection
$conn->close();
?>
