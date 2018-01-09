#Database tables struct
##users
|id|name|password| 
|---|---|---|
|#1|root|***|
|#2|others|***|

##groups
|id|name|is_admin|
|---|---|---|
|#1|admin|true|
|#2|user|false|

##group_users
|group_id|user_id| 
|---|---|
|#1|#1|
|#2|#2|

##games
|id|name| 
|---|---|
|#1|World of Warcraft|
|#2|League of Legends|

##managers
|id|name| 
|---|---|
|#1|manager|
|#2|visitor|

##game_managers
|game_id|group_id|manager_id| 
|---|---|---|
|#1|#1|#1|
|#1|#2|#2|
|#2|#1|#1|

##authorizations
|type|id|name|allow|attribute|operation|value|priority|
|---|---|---|---|---|---|---|---|
|scene|login|in|allow| | | |1|
|scene|panel|in|allow|this:id|\>|0|1|
|scene|admin|in|allow|group:is_admin|=|1|1|
|game|#1|show|allow|manager::count|>|0|1|
|game|#1|controller.action.show|allow|manager:id|=|#1|1|



