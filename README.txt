///////////////
//The Process//
///////////////

This works in two parts. 

First, a gedcom file is uploaded and the data from the file is then loaded into a MySQL database. The gedcom can be discarded at the end of this. This operation erases any data that is already stored in the database.

Second, the user selects a name to be at the center of a circle chart and a circle chart is generated from the data in the MySQL database. A giant PNG image is generated and saved in a folder with today's date and under the name of the seed individual.


////////////////////////////////////
//Installation/System Requirements//
////////////////////////////////////

System Requirements: A web server running PHP version 4+ and a MySQL database server which could be installed on the same server. A high memory cap is also desirable for faster loading. 

Installation

	1. Create an empty MySQL database. Don't worry about the tables.
	2. Edit db.php to reflect the necessary credentials in order to connect to your new and empty database.
	3. Put all files in this folder in your htdocs or wwwroot directory.
	4. Check in a web browser to see if it is working by uploading a gedcom file and generate a circle chart.


///////////////////
//About the Files//
///////////////////

index.php - The gedcom upload form.
gedcom.php - The actual gedcom to MySQL middleware. 
gedcom_all.php - Will import ALL of the gedcom data into a MySQL database. (optional)
circle.php - The seed name selection form.
binomial.php - The circle chart generating software.
db.php - Database connection include

The ajax directory - Javascript files relevant to circle.php

///////////////
//Adjustments//
///////////////

Gedcom.php
	The additional parsing of certain irrelevant things is necessary to account for certain anomalies that pop up in larger and often more messy gedcom files. The if/else statements should be left alone. However, the contents of the if/else statments can be commented out so no unnecessary processing is done.

Binomial.php
	By default, binomial is set to generate a 10 generation circle chart. To change the number of generations, change the value of $number_of_rings on line 6 to whatever value you prefer. 10 is the maximum.

///////////
//Contact//
///////////

Email: jfekendall@gmail.com
Phone: 419.203.8450
