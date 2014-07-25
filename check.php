<?php

/**
 * @author Oliver Lorenz <mail@oliverlorenz.com>
 * @since 04.05.14
 */

$addressList = array();

unset($argv[0]);

if (empty($argv)) {
    die("nothing configured!\n");
}

function activate()
{
    echo "activate!\n";
    system('export $( grep -ao "DBUS_SESSION_BUS_ADDRESS=[^\0]*"  /proc/$(pidof -s gnome-screensaver)/environ ); gnome-screensaver-command -d');
}

function deactivate()
{
    echo "deactivate!\n";
    system('export $( grep -ao "DBUS_SESSION_BUS_ADDRESS=[^\0]*"  /proc/$(pidof -s gnome-screensaver)/environ ); gnome-screensaver-command -a');
}

$sortedList = $argv;
while(true) {

    $somebodyIsHome = false;
    foreach ($sortedList as $index => $address) {
        echo "Look! In ". $address;

        $pingAddress = $sortedList[$index];
        unset($sortedList[$index]);
        $output = array();

        exec("ping -c 1 -W 20 $pingAddress", $output, $status);
        if(preg_match('/[1-9]+ received/', $output[4])) {
            $somebodyIsHome = true;
            echo "'s window is light!\n";
            array_unshift($sortedList, $pingAddress);
            break;
        } else {
            $somebodyIsHome = false;
            echo " is not home\n";
            array_push($sortedList, $pingAddress);
        }
    }
    if ($somebodyIsHome) {
        activate();
    } else {
        deactivate();
    }
    sleep(5);
}