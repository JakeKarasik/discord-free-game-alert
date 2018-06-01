# discord-free-game-alert

# Required files:  
games.txt (empty file, inside of triggers folder)  
log.txt (empty file, inside of actions folder)  
key.txt (get service key from https://platform.ifttt.com/services/<YOUR_SERVICE_NAME>/api, place in root directory)  

# Basic Instructions:  
1. Create a new applet.  
2. Add included trigger.  
3. Add included action.  
4. Add 4 action fields: channel_id (chosen by user), game_title (hidden from user), game_link (hidden from user), webhook_link (chosen by user).  
5. Fill in channel_id of desired discord text channel.  
6. Open discord (must be server admin), create webhook, copy webhook link.  
7. Fill in webhook link for desired discord server.  
