#!/usr/bin/env bash
scriptname=$(basename "$(test -L "$0" && readlink "$0" || echo "$0")")
dirInstall="/opt/drmphp"

#################################
#       F U N C T I O N S       #
#################################

function isRoot() {
  if [ $EUID -ne 0 ]; then
    return 1
  fi
}

function checkRunning() {
  runningPid=$(ps -ax|grep -i apache|grep -v grep|grep -v "$scriptname"|awk '{print $1}')
  echo $runningPid
}

function getIP() {
  [ -z "`which dig`" ] && serverIP=$(host myip.opendns.com resolver1.opendns.com | tail -n1 | cut -d' ' -f4-) || serverIP=$(dig +short myip.opendns.com @resolver1.opendns.com)
  # echo $serverIP
}

function checkOS() {
  if [[ -e /etc/debian_version ]]; then
    OS="debian"
    PKGS='git software-properties-common iputils-ping dnsutils apache2 mysql-server aria2'
    source /etc/os-release
    if [[ $ID == "debian" ]]; then
      if [[ $VERSION_ID -lt 8 ]]; then
        echo " Your Debian version is unsupported."
        echo ""
        echo " Script supports only Debian >=8"
        echo ""
        exit 1
      fi
    elif [[ $ID == "ubuntu" ]]; then
  OS="ubuntu"
  MAJOR_UBUNTU_VERSION=$(echo "$VERSION_ID" | cut -d '.' -f1)
  if [[ $MAJOR_UBUNTU_VERSION -lt 18 ]]; then
    echo "Your Ubuntu version is unsupported."
    echo ""
    echo "Script supports only Ubuntu >=18"
    echo ""
    exit 1
  fi
fi

if ! dpkg -s $PKGS >/dev/null 2>&1; then
  echo "Installing missing packages…"
  sleep 1
  apt-get update -y;

  if [[ $MAJOR_UBUNTU_VERSION == 18 ]]; then
    apt -y install $PKGS;
    add-apt-repository ppa:ondrej/php -y;
    apt-get update -y;
    apt -y install php7.2 php7.2-cli php7.2-json php7.2-common php7.2-mysql php7.2-zip php7.2-gd php7.2-mbstring php7.2-curl php7.2-xml php7.2-bcmath php7.2-bz2 php7.2-xmlrpc;
  elif [[ $MAJOR_UBUNTU_VERSION == 20 || $MAJOR_UBUNTU_VERSION == 22 ]]; then
    apt -y install $PKGS;
    add-apt-repository ppa:ondrej/php -y;
    apt-get update -y;
    apt -y install php7.4 php7.4-cli php7.4-json php7.4-common php7.4-mysql php7.4-zip php7.4-gd php7.4-mbstring php7.4-curl php7.4-xml php7.4-bcmath php7.4-bz2 php7.4-xmlrpc;
  fi
fi
  elif [[ -e /etc/system-release ]]; then
    source /etc/os-release
    if [[ $ID == "fedora" || $ID_LIKE == "fedora" ]]; then
      OS="fedora"
      dnf -y update
      dnf -y install git xz iputils which initscripts
      dnf -y install httpd
      dnf -y install aria2
      dnf -y install community-mysql-server
      dnf -y install https://rpms.remirepo.net/fedora/remi-release-"$VERSION_ID".rpm
      dnf config-manager --set-enabled remi
      dnf module reset php -y
      dnf module install php:remi-7.4 -y
      dnf install php74-php-cli php74-php-common php74-php-json php74-php-gd php74-php-curl php74-php-mysqlnd php74-php-zip php74-php-xml php74-php-mbstring php74-php-bcmath -y
    fi
    if [[ $ID == "centos" || $ID == "rocky" || $ID == "redhat" ]]; then
      OS="centos"
      if [[ ! $VERSION_ID =~ (7|8) ]]; then
        echo " Your CentOS/RockyLinux/RedHat is unsupported."
        echo ""
        echo " Installer supports CentOS/RockyLinux/RedHat 7 and 8."
        echo ""
        exit 1
      fi
      yum update -y
      yum install git xz iputils which initscripts -y
      yum install httpd -y
      yum install aria2 -y
      yum install mysql-server -y
      yum install epel-release yum-utils -y
      yum install http://rpms.remirepo.net/enterprise/remi-release-"$VERSION_ID".rpm -y
      yum-config-manager --enable remi-php74
      yum update -y
      yum install php php-json php-common php-cli php-gd php-curl php-mysqlnd php-zip php-xml php-mbstring pphp-bcmath -y
    fi
  else
    echo " Looks like you're launching this installer in other OS than Debian, Ubuntu, Fedora or CentOS."
    exit 1
  fi
}

function checkArch() {
  case $(uname -m) in
    x86_64) architecture="amd64" ;;
    *) { echo " Unsupported Arch. Can't continue."; exit 1; }
  esac
}

function checkInternet() {
  [ -z "`which ping`" ] && echo " First install iputils-ping" && exit 1
  echo " Checking Internet access…"
  if ! ping -c 2 google.com &> /dev/null; then
    echo " - No Internet. Check your network and DNS settings."
    exit 1
  fi
  echo " - Have Internet Access"
}

function initialCheck() {
  if ! isRoot; then
    echo " Script must run as root or user with sudo privileges. Example: sudo $scriptname"
    exit 1
  fi
  checkOS
  checkArch
  checkInternet
}

function installDRMPHP() {
  echo " Installing and configuring DRMPHP..."

  echo " Downloading files..."
  [[ ! -d "$dirInstall" ]] && mkdir -p ${dirInstall}
  git clone https://github.com/DRM-Scripts/DRMPHP $dirInstall
  echo " Downloaded."

  echo " Setting up MySQL..."
  content="[mysqld]\nsql-mode=\"NO_ENGINE_SUBSTITUTION\"\n"
  echo -e "$content" | tee /etc/mysql/my.cnf > /dev/null
  service mysql restart
  echo " MySQL configured successfully!"

  echo " Configuring PHP..."
  sed -i -r 's/short_open_tag = Off$/short_open_tag = On/' /etc/php/7.4/cli/php.ini
  sed -i -r 's/short_open_tag = Off/short_open_tag = On/g' /etc/php/7.4/apache2/php.ini
  echo " PHP configured successfully!"

  read -p ' Change DRMPHP web-port? (Yes/No) ' answer_cp </dev/tty
  if [ "$answer_cp" != "${answer_cp#[Yy]}" ]; then
    read -p ' Enter port number: ' answer_port </dev/tty
    if [[ $OS == "debian" ]] || [[ $OS == "ubuntu" ]]; then 
      sed -i "s/80/${answer_port}/" /etc/apache2/ports.conf
      sed -i "s/80/${answer_port}/" /etc/apache2/sites-enabled/000-default.conf
    else
      sed -i "s/80/${answer_port}/" /etc/httpd/conf/httpd.conf
    fi
  else
    answer_port="80"
  fi
  echo " Configuring permissions..."
  line="www-data ALL=(ALL) NOPASSWD: ALL";
  sed -i "$ a $line" /etc/sudoers;
  echo "Sudoers configured successfully!";

  service apache2 restart;

  if [[ ! -f "/usr/bin/ffmpeg" ]] || [[ ! -f "/usr/bin/ffprobe" ]]; then
    echo "FFMpeg not found, installing...";
    curl -L --progress-bar -# -o "$dirInstall/ffmpeg.tar.xz" "https://johnvansickle.com/ffmpeg/releases/ffmpeg-release-amd64-static.tar.xz";
    [[ ! -d "$dirInstall/ffmpeg" ]] && mkdir -p "$dirInstall/ffmpeg"
    tar -xf "$dirInstall/ffmpeg.tar.xz" -C "$dirInstall/ffmpeg";
    for i in "$dirInstall"/ffmpeg/ffmpeg-*-amd64-static; do cp "$i"/ff* /usr/bin/; break; done
    echo "FFMpeg installed!";
  fi

  cp -r "$dirInstall/panel/." /var/www/html;
  cd /var/www/html
  
  chmod +x mp4decrypt
  [[ ! -d "./download" ]] && mkdir -p "download"
  chmod 777 download;

  cd ../;
  [[ ! -d "./backup" ]] && mkdir -p "backup"
  chmod 777 backup;
  chmod 777 html;
  echo " Panel configured successfully!";
  
  if [ -f /root/.my.cnf ]; then
    read -s -p "Please enter root user MySQL password (password will be hidden when typing): " rootpasswd </dev/tty
  else
    read -p "Please create root user MySQL password: " rootpasswd </dev/tty
  fi
  echo "";
	read -p "Please enter the NAME of the new MySQL database (example: db1): " dbname </dev/tty
	sed -i "s/drm/$dbname/g" /var/www/html/_db.php
	read -p "Please enter the MySQL database CHARACTER SET (enter utf8 if you don't know what you are doing): " charset </dev/tty
	echo "Creating new MySQL database..."
	mysql -uroot -p${rootpasswd} -e "CREATE DATABASE ${dbname} /*\!40100 DEFAULT CHARACTER SET ${charset} */;"
	echo "Database successfully created!"
  read -p  "Please enter the NAME of the new MySQL database user (example: user1): " username </dev/tty
	sed -i "s/admin/$username/g" /var/www/html/_db.php
	read -s -p "Please enter the PASSWORD for the new MySQL database user (password will be hidden when typing): " userpass </dev/tty
	sed -i "s/passwd/$userpass/g" /var/www/html/_db.php
	echo "Creating new user..."
	mysql -uroot -p${rootpasswd} -e "CREATE USER ${username}@localhost IDENTIFIED BY '${userpass}';"
	echo "User successfully created!"
	echo ""
	echo "Granting ALL privileges on ${dbname} to ${username}!"
	mysql -uroot -p${rootpasswd} -e "GRANT ALL PRIVILEGES ON ${dbname}.* TO '${username}'@'localhost';"
	mysql -uroot -p${rootpasswd} -e "FLUSH PRIVILEGES;"
	mysql -uroot -p${rootpasswd} ${dbname} < "$dirInstall/db.sql"
	# MySQL commands
  commands=$(cat <<EOF
    USE $dbname;
    SET GLOBAL sql_mode = 'NO_ENGINE_SUBSTITUTION';
    SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION';
EOF
    )
	echo "$commands" | mysql -u root -p"$rootpasswd"

  # Delete default apache page
  rm /var/www/html/index.html;
}

function cleanup() {
  rm -rf $dirInstall;
}

#####################################
#     E N D   F U N C T I O N S     #
#####################################
echo ""
echo "#############################################################"
echo "#     DRMPHP install and configuration script for Linux     #"
echo "#############################################################"
echo ""

while true; do
  echo ""
  read -p " This script will install DRMPHP on your system. Continue? (Yes|No) " yn </dev/tty
  case $yn in
    [Yy]*)
      initialCheck
      installDRMPHP
      cleanup
      break
      ;;
    [Nn]*)
      break
      ;;
    *) echo " Enter Yes or No"
      ;;
  esac
done

getIP
echo "####################################################";
echo "#                  PANEL DETAILS                   #";
echo "####################################################";
echo "USER: admin";
echo "PASS: Admin@2023##";
echo "URL: http://${serverIP}:${answer_port}/login.php";
echo "----------------------------------------------------";
echo "NOTE: EDIT <M3U8 Download URL> IN SETTINGS PAGE";
echo "";
echo "Have Fun!"
echo ""
sleep 3
