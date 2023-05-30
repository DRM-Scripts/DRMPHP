#!/bin/bash

#-- UBUNTU 18.04 ONLY --

echo "############################################################################################"
echo "#               DRMPHP 0.1 BY DRMSCRIPTS COMMUNITY - HTTPS://DRMSCRIPTS.COM                #"
echo "# THIS INSTALL IS BASED ON INSTALLING ALL FILES IN PANEL FOLDER TO THE ROOT OF HTML FOLDER #"
echo "############################################################################################"

echo "####################################################"
echo "# INSTALL STEP 1: REPOS, PACKAGES & PANEL DOWNLOAD #"
echo "####################################################"

# Add Repos
apt install software-properties-common -y;
add-apt-repository ppa:ondrej/php -y;
add-apt-repository ppa:ondrej/apache2 -y;
add-apt-repository ppa:xapienz/curl34 -y;

# Remove any pending packages
apt-get autoremove;

# Run an update
apt-get update -y;
apt full-upgrade -y;

# Install MySQL, Apache2 & Aria2
apt install mysql-server apache2 aria2 -y;
apt-get install php7.4 php7.4-cli php7.4-json php7.4-common php7.4-mysql php7.4-zip php7.4-gd php7.4-mbstring php7.4-curl php7.4-xml php7.4-bcmath php7.4-bz2 php7.4-xmlrpc -y;

# Download Panel
cd /home;
apt install git -y;
git clone https://github.com/DRM-Scripts/DRMPHP;

echo "####################################################";
echo "# INSTALL STEP 2: MYSQL, SHORTTAGS, FFMPEG & PANEL #";
echo "####################################################";

#setup sql cnf
content="[mysqld]\nsql-mode=\"NO_ENGINE_SUBSTITUTION\"\n";
echo -e "$content" | tee /etc/mysql/my.cnf > /dev/null;
service mysql restart;
echo "MySQL configured successfully!";

#setup php.ini
sed -i -r 's/short_open_tag = Off$/short_open_tag = On/' /etc/php/7.4/cli/php.ini;
sed -i -r 's/short_open_tag = Off/short_open_tag = On/g' /etc/php/7.4/apache2/php.ini;
echo "php.ini configured successfully!";

#setup sudoers
line="www-data ALL=(ALL) NOPASSWD: ALL";
sed -i "$ a $line" /etc/sudoers;
echo "Sudoers configured successfully!";

service apache2 restart;
cd /home;

#download and setup ffmpeg
wget https://johnvansickle.com/ffmpeg/releases/ffmpeg-release-amd64-static.tar.xz;
tar -xf ffmpeg-release-amd64-static.tar.xz;
cp -r ffmpeg-6.0-amd64-static/* /usr/bin;
echo "FFMpeg configured successfully!";

cd DRM*;
cp -r panel/. /var/www/html;
cd /var/www/html;
chmod +x mp4decrypt;
mkdir download;
chmod 777 download;
cd ../;
mkdir backup;
chmod 777 backup;
chmod 777 html;
cd /home;
cd DRM*;
cp panel/downloader.php /var/www/html;
echo "Panel configured successfully!";

echo "####################################################";
echo "# INSTALL STEP 3: MYSQL DATABASE SETUP             #";
echo "####################################################";

cd /home/DRMPHP;
chmod 777 ./db.sh;
sed -i -e 's/\r$//' db.sh;
./db.sh;
echo "Database configured successfully!";

echo "####################################################";
echo "# INSTALL STEP 4: CLEANUP                          #";
echo "####################################################";

#delete default apache page
rm /var/www/html/index.html;
echo 
echo
echo "####################################################";
echo "#              INSTALLATION COMPLETE               #";
echo "####################################################";
echo 
echo
#--ACCESS
public_ip=$(wget -q "http://api.ipify.org" -O -);
echo "####################################################";
echo "#                  PANEL DETAILS                   #";
echo "####################################################";
echo "# USER: admin                                      #";
echo "# PASS: Admin@2023##                               #";
echo "# URL: http://$public_ip/login.php                 #";
echo "####################################################";
echo "# NOTE: EDIT <M3U8 Download URL> IN SETTINGS PAGE  #";
echo "####################################################";
