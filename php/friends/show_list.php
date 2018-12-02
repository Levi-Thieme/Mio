<?php
    function show_list($sql, $connection) {
        // execute query
        $result = $connection->query($sql) or die(mysqli_error($connection));           
    
        // check whether we found a row
        while ($user = $result->fetch_assoc())
        {
            echo "<div>" . implode($user) . "</div>";
        }
    }
?>