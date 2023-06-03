**Weather App**

This is a weather application that provides weather information for "South Gloucestershire" by default, but also allows users to search for weather information in other cities. The application stores weather data for South Gloucestershire for the past 7 days, saving it to both local storage and a database.

**Installation**

To use the Weather App, follow these steps:

Clone the repository or download the source code.

Ensure you have a web server installed (e.g., Apache, Nginx).

Copy the project files to the web server's document root directory.

Open the index2.php file and update it with all the required codes (as mentioned in the instructions).

Delete the insert_weather_data.php and update_weather.php files, as they are no longer needed.

Delete the script1.js file if it is present.

Set up a database (e.g., MySQL) and create a table to store the weather data. Use the following SQL code to create the table:

sql
Copy code
------------------------------------------------------------------------------------------
CREATE TABLE `weatherstable` (
  `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
  `description` VARCHAR(255) NOT NULL,
  `temperature` DECIMAL(5, 2) NOT NULL,
  `wind` DECIMAL(5, 2) NOT NULL,
  `city` VARCHAR(255) NOT NULL,
  `humidity` INT(3) NOT NULL,
  `icon` VARCHAR(50) NOT NULL,
  `dt` DATETIME NOT NULL
);

-----------------------------------------------------------------------------------------------

This code creates a table named weatherstable with the necessary columns to store the weather data.

Update the necessary database configuration in the code to connect to your database.

Access the app through your web browser by visiting the appropriate URL (e.g., http://localhost/weather-app).

**Usage**

The Weather App displays the weather information for South Gloucestershire by default. To search for weather information in other cities, follow these steps:

Enter the name of the desired city in the search bar.
Press the Enter key or click the search button.
The app will retrieve and display the current weather information for the specified city.
The weather data will be saved to both local storage and the database for future reference.
The app also provides offline mode functionality, which allows you to access previously fetched weather data when you are offline. To use the offline mode, ensure you have fetched the weather data within the past hour while being online.

**Support**

If you encounter any issues or have any questions, please feel free to contact the app developer at [email protected]

**Demo**
To see a live demo of the Weather App, visit rijanswebapp.infinityfreeapp.com

License
The Weather App is open source software licensed under the MIT License.
