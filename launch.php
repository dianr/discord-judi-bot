<?php
include __DIR__.'/vendor/autoload.php';
define("BOT_NAME", "Judi");

use Discord\Discord;

$discord = new Discord([
    'token' => 'MjEwMDIwODg0MDEyODU5Mzky.CoIsuA.6IJhAw_zgsRinm6OHe9iKopYVlc',
]);


$discord->on('ready', function ($discord) {
    echo "Bot is ready!", PHP_EOL;
  	$GLOBALS["judi"]  = 'disabled';
  	$GLOBALS["result"]  = array();
  	$GLOBALS["rank"] = array();
    print_r($GLOBALS["judi"]);
    // Listen for messages.
    $discord->on('message', function ($message, $discord) {
        echo "{$message->author->username}: {$message->content}",PHP_EOL;
        
        //Judi start----------------------------------------------------------------------------------
        if (preg_match("/^\/judi start/i", $message->content))
        {
        	$GLOBALS["judi"] = 'enabled';
        	//print_r($judi);
        	$explosion = explode( ' ', $message->content );
        	if(is_numeric($explosion[2]))
        	{	
	    		$start_message = "Let's gamble! type /roll ".$explosion[2]." to participate. type /endroll when finished.";
	    		$GLOBALS["result"] = [];
    		}

    		else
    		{
    			$start_message = "Error, number must be an integer [2 - 9999999]";
    		}	

    		$message->channel->sendMessage($start_message);
        }

        else
        //Roll start----------------------------------------------------------------------------------
        if (preg_match("/^\/roll/i", $message->content))
        {
        	if($GLOBALS["judi"] == 'enabled')
        	{
	        	$explosion = explode( ' ', $message->content );
	        	if(is_numeric($explosion[1]))
	        	{	
	        		if(array_search($message->author->username, array_column($GLOBALS["result"], 'username')) === FALSE)
	        		{
		        		$number = rand(0,$explosion[1]);
			    		$start_message = $message->author->username." rolled ".$number.".";
			    		$newdata =  array (
			    		      'username' => $message->author->username,
			    		      'roll' 	 => $number
			    		    );
			    		$GLOBALS["result"][] = $newdata;
			    		$message->channel->sendMessage($start_message);
			    	}
			    	else
			    	{
			    		$start_message = $message->author->username." already rolled!";
			    		$message->channel->sendMessage($start_message);
			    	}
	    		}

	    		else
	    		{
	    			$start_message = $message->author->username." has an invalid roll.";
	    			$message->channel->sendMessage($start_message);
	    		}	
	    	}

    		
        }

        //End roll----------------------------------------------------------------------------------
        if (preg_match("/^\/endroll/i", $message->content))
        {
        	$GLOBALS["judi"] = 'disabled';
        	if(count($GLOBALS["result"]) <2)
        	{
        		$message->channel->sendMessage("Not enough players. Judi has been reset.");
        	}
        	else
        	{
        		$winningNumber = max(array_column($GLOBALS["result"], 'roll')); echo $winningNumber."\n";
        		$losingNumber = min(array_column($GLOBALS["result"], 'roll')); echo $losingNumber."\n";
        		$winningKey = array_search($winningNumber, array_column($GLOBALS["result"], 'roll'));
        	    $losingKey = array_search($losingNumber, array_column($GLOBALS["result"], 'roll'));
        	    $winner = $GLOBALS["result"][$winningKey]['username'];	echo $winner."\n";
        	    $loser = $GLOBALS["result"][$losingKey]['username']; echo $loser."\n";
        	    $diff = $winningNumber - $losingNumber;
        		$message->channel->sendMessage("$loser owes $winner $diff gold");
        		$GLOBALS["rank"][] = "$loser owes $winner $diff gold";
        	}
			
	    }

        //Klasemen----------------------------------------------------------------------------------
        if (preg_match("/^\/rank/i", $message->content))
        {
        	var_dump($GLOBALS["rank"]);
			$message->channel->sendMessage(json_encode($GLOBALS["rank"]));
	    }

        //Dump Data----------------------------------------------------------------------------------
        if (preg_match("/^\/dumpdata/i", $message->content))
        {
        	var_dump($GLOBALS["result"]);
        	print_r(array_column($GLOBALS["result"], 'roll'));
			$message->channel->sendMessage(json_encode($GLOBALS["result"]));
	    }
	    	

    	
    });
});

$discord->run();