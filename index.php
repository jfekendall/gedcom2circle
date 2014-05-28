<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        echo "<h1>Upload a Gedcom File</h1>
        <p>Please note: this will erase all names, families, events and notes already in the database!</p>
        <form method=POST action='gedcom.php' enctype=multipart/form-data>
            <input name='gedcom' type=file accept='ged'><br>
            <input type='submit' value='Upload'>
        </form>";
        ?>
    </body>
</html>
