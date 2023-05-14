#!/bin/bash
# Bash script written by Saad Ismail - me@saadismail.net

# If /root/.my.cnf exists then it won't ask for root password
if [ -f /root/.my.cnf ]; then
	echo "Please enter the NAME of the new MySQL database! (example: database1)"
	read dbname
	echo "Please enter the MySQL database CHARACTER SET! (example: latin1, utf8, ...)"
	echo "Enter utf8 if you don't know what you are doing"
	read charset
	echo "Creating new MySQL database..."
	sudo mysql -e "CREATE DATABASE ${dbname} /*\!40100 DEFAULT CHARACTER SET ${charset} */;"
	echo "Database successfully created!"
	echo "Showing existing databases..."
	sudo mysql -e "show databases;"
	echo ""
	echo "Please enter the NAME of the new MySQL database user! (example: user1)"
	read username
	echo "Please enter the PASSWORD for the new MySQL database user!"
	echo "Note: password will be hidden when typing"
	read -s userpass
	echo "Creating new user..."
	sudo mysql -e "CREATE USER ${username}@localhost IDENTIFIED BY '${userpass}';"
	echo "User successfully created!"
	echo ""
	echo "Granting ALL privileges on ${dbname} to ${username}!"
	sudo mysql -e "GRANT ALL PRIVILEGES ON ${dbname}.* TO '${username}'@'localhost';"
	sudo mysql -e "FLUSH PRIVILEGES;"
	sudo mysql ${dbname} < db.sql
    	sudo mysql SET GLOBAL sql_mode = 'NO_ENGINE_SUBSTITUTION';
    	sudo mysql SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION';
	echo "You're good now :)"
	exit
	
# If /root/.my.cnf doesn't exist then it'll ask for root password	
else
	echo "Please enter root user MySQL password!"
	echo "Note: password will be hidden when typing"
	read -s rootpasswd
	echo "Please enter the NAME of the new MySQL database! (example: database1)"
	read dbname
	echo "Please enter the MySQL database CHARACTER SET! (example: latin1, utf8, ...)"
	echo "Enter utf8 if you don't know what you are doing"
	read charset
	echo "Creating new MySQL database..."
	sudo mysql -uroot -p${rootpasswd} -e "CREATE DATABASE ${dbname} /*\!40100 DEFAULT CHARACTER SET ${charset} */;"
	echo "Database successfully created!"
	echo "Showing existing databases..."
	sudo mysql -uroot -p${rootpasswd} -e "show databases;"
	echo ""
	echo "Please enter the NAME of the new MySQL database user! (example: user1)"
	read username
	echo "Please enter the PASSWORD for the new MySQL database user!"
	echo "Note: password will be hidden when typing"
	read -s userpass
	echo "Creating new user..."
	sudo mysql -uroot -p${rootpasswd} -e "CREATE USER ${username}@localhost IDENTIFIED BY '${userpass}';"
	echo "User successfully created!"
	echo ""
	echo "Granting ALL privileges on ${dbname} to ${username}!"
	sudo mysql -uroot -p${rootpasswd} -e "GRANT ALL PRIVILEGES ON ${dbname}.* TO '${username}'@'localhost';"
	sudo mysql -uroot -p${rootpasswd} -e "FLUSH PRIVILEGES;"
	sudo mysql -uroot -p${rootpasswd} ${dbname} < db.sql
    	sudo mysql -uroot -p${rootpasswd} SET GLOBAL sql_mode = 'NO_ENGINE_SUBSTITUTION';
    	sudo mysql -uroot -p${rootpasswd} SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION';
	echo "You're good now :)"
	exit
fi
