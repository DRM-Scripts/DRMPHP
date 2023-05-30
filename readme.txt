-- UBUNTU 18.04 ONLY --

cd /home
wget https://github.com/DRM-Scripts/DRMPHP/installer-beta.sh
chmod 777 ./installer-beta.sh && sed -i -e 's/\r$//' installer-beta.sh && ./installer-beta.sh
