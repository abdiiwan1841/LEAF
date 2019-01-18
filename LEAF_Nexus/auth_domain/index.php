<?php
/*
 * As a work of the United States government, this project is in the public domain within the United States.
 */

/*
    Authenticator
    Date: March 8, 2013

*/

include '../globals.php';
include '../sources/Login.php';
include '../db_mysql.php';
include '../config.php';
if (!class_exists('XSSHelpers'))
{
    require_once dirname(__FILE__) . '/../libs/php-commons/XSSHelpers.php';
}

$config = new Orgchart\Config();
$db = new DB($config->dbHost, $config->dbUser, $config->dbPass, $config->dbName);

$login = new Orgchart\Login($db, $db);

if (isset($_SERVER['REMOTE_USER']) && (!isset(Orgchart\Config::$leafSecure) || Orgchart\Config::$leafSecure == false))
{
    $protocol = 'http://';
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
    {
        $protocol = 'https://';
    }
    $redirect = '';
    if (isset($_GET['r']))
    {
        $redirect = $protocol . $_SERVER['HTTP_HOST'] . base64_decode($_GET['r']);
    }
    else
    {
        $redirect = $protocol . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/../';
    }

    list($domain, $user) = explode('\\', $_SERVER['REMOTE_USER']);

    // see if user is valid
    $vars = array(':userName' => $user);
    $res = $db->prepared_query('SELECT * FROM employee
    										WHERE userName=:userName
												AND deleted=0', $vars);

    if (count($res) > 0)
    {
        $_SESSION['userID'] = $user;
        session_write_close();
        header('Location: ' . $redirect);
        exit();
    }
    else
    {
        // try searching through national database
        $globalDB = new DB(DIRECTORY_HOST, DIRECTORY_USER, DIRECTORY_PASS, DIRECTORY_DB);
        $vars = array(':userName' => $user);
        $res = $globalDB->prepared_query('SELECT * FROM employee
											LEFT JOIN employee_data USING (empUID)
											WHERE userName=:userName
    											AND indicatorID = 6
												AND deleted=0', $vars);
        // add user to local DB
        if (count($res) > 0)
        {
            $vars = array(':empUID' => $res[0]['empUID'],
                    ':firstName' => $res[0]['firstName'],
                    ':lastName' => $res[0]['lastName'],
                    ':middleName' => $res[0]['middleName'],
                    ':userName' => $res[0]['userName'],
                    ':phoFirstName' => $res[0]['phoneticFirstName'],
                    ':phoLastName' => $res[0]['phoneticLastName'],
                    ':domain' => $res[0]['domain'],
                    ':lastUpdated' => time(), );
            $db->prepared_query('INSERT INTO employee (empUID, firstName, lastName, middleName, userName, phoneticFirstName, phoneticLastName, domain, lastUpdated)
        							VALUES (:empUID, :firstName, :lastName, :middleName, :userName, :phoFirstName, :phoLastName, :domain, :lastUpdated)
    								ON DUPLICATE KEY UPDATE deleted=0', $vars);

            $vars = array(':empUID' => XSSHelpers::xscrub($res[0]['empUID']),
                    ':indicatorID' => 6,
                    ':data' => $res[0]['data'],
                    ':author' => 'viaLogin',
                    ':timestamp' => time(),
            );
            $db->prepared_query('INSERT INTO employee_data (empUID, indicatorID, data, author, timestamp)
											VALUES (:empUID, :indicatorID, :data, :author, :timestamp)
    										ON DUPLICATE KEY UPDATE data=:data', $vars);

            // redirect as usual
            $_SESSION['userID'] = $res[0]['userName'];
            session_write_close();
            header('Location: ' . $redirect);
            exit();
        }
        else
        {
            header('Refresh: 4;URL=' . $login->parseURL(dirname($_SERVER['PHP_SELF'])) . '/..' . '/login/index.php');

            echo 'Unable to log in: User not found in global database.  Redirecting back to PIV login screen.';
        }
    }
}
else
{
    header('Refresh: 4;URL=' . $login->parseURL(dirname($_SERVER['PHP_SELF'])) . '/..' . '/login/index.php');

    echo 'Unable to log in: Domain logon issue.  Redirecting back to PIV login screen.';
}
