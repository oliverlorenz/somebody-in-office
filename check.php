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

    $countOfPeopleInOffice = 0;
    $newSortedList = array();
    foreach ($sortedList as $index => $pingAddress) {
    	$output = array();
		echo $pingAddress . ' ';
        
        $thereIsSomebody = false;
        $command = "ping -c 1 -W 1 $pingAddress";
        // echo "\n$command\n";
    	@exec("ping -c 1 -W 1 $pingAddress", $output, $status);
        foreach ($output as $line) {
        	if(preg_match('/[1-9]+ received/', $line)) {
    			$thereIsSomebody = true;
        	}
        }

        if ($thereIsSomebody) {
        	array_unshift($newSortedList, $pingAddress);
        	foreach ($sortedList as $value) {
        		if ($value != $pingAddress) {
        			array_push($newSortedList, $value);
        		}
        	}
        	$countOfPeopleInOffice++;
        	echo 'is in office';
        	echo "\n";
        	break;
        } else {
        	array_push($newSortedList, $pingAddress);
        	echo 'is not in office';
        	echo "\n";
        }
        
    }
    $sortedList = $newSortedList;
    if (!empty($countOfPeopleInOffice)) {
        activate();
    } else {
        deactivate();
    }
    sleep(5);
}
