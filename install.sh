#!/bin/bash

#-- UBUNTU 18.04 ONLY --

###########################################################################################
# THIS INSTALL IS BASED ON INSTALLING ON FILES IN PANEL FOLDER TO THE ROOT OF HTML FOLDER #
###########################################################################################

#--REQUIREMENTS
sudo apt update
sudo apt -y install software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt full-upgrade -y
sudo apt install mysql-server apache2 aria2 -y
sudo apt-get install -y php7.4 php7.4-cli php7.4-json php7.4-common php7.4-mysql php7.4-zip php7.4-gd php7.4-mbstring php7.4-curl php7.4-xml php7.4-bcmath php7.4-bz2 php7.4-xmlrpc

#--DOWNLOAD
cd /home
sudo apt install git
#git clone https://github.com/DRM-Scripts/DRMPHP
#git clone https://github.com/andrewzhong1122/DRMPHP
git clone https://github.com/DevataDev/DRMUniversal.git

#--SETUP

#setup sql cnf
content="[mysqld]\nsql-mode=\"NO_ENGINE_SUBSTITUTION\"\n"
echo -e "$content" | sudo tee /etc/mysql/my.cnf > /dev/null
sudo service mysql restart
echo "mysql configured successfully!"

#setup php.ini
cd DRMPHP/
sudo cp -r cli /etc/php/7.4/
sudo sed -i 's/short_open_tag = Off/short_open_tag = On/g' /etc/php/7.4/apache2/php.ini
echo "php.ini configured successfully!"

#setup sudoers
line="www-data ALL=(ALL) NOPASSWD: ALL"
sudo sed -i "$ a $line" /etc/sudoers
echo "Sudoers configured successfully!"

sudo service apache2 restart
cd /home

#download and setup ffmpeg
sudo wget https://johnvansickle.com/ffmpeg/releases/ffmpeg-release-amd64-static.tar.xz
sudo tar -xf ffmpeg-release-amd64-static.tar.xz
sudo cp -r ffmpeg-6.0-amd64-static/* /usr/bin

cd DRM*
sudo cp -r panel/. /var/www/html/panel
cd /var/www/html/panel
sudo chmod +x mp4decrypt
sudo mkdir download
sudo chmod 777 download
cd ../
mkdir backup
sudo chmod 777 backup
sudo chmod 777 panel #
cd /home
cd DRM*
sudo cp panel/downloader.php /var/www/html/panel
cd /home/DRM*
sudo chmod 777 ./db.sh
sudo sed -i -e 's/\r$//' db.sh
sudo ./db.sh #- Fill in the MYSQL Database & User Details

echo 
echo
# Prompt for MySQL ROOT credentials
#read -s -p "Enter MySQL ROOT password: " rootpassword
#echo
#read -p "Enter database name you just created: " dbname

# MySQL commands
#commands=$(cat <<EOF
#USE $dbname;
#SET GLOBAL sql_mode = 'NO_ENGINE_SUBSTITUTION';
#SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION';
#EOF
#)

#echo "$commands" | mysql -u root -p"$rootpassword"
#echo
#echo
#sudo service apache2 restart
echo "Installation completed!"


#--ACCESS
public_ip=$(wget -q "http://api.ipify.org" -O -)
echo "Login with admin/Admin@2022## @ http://$public_ip/panel/login.php"
echo "Edit <M3U8 Download URL> in the setting"
