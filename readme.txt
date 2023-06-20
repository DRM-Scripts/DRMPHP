NOTICE: The adding of user lines is a work in progress, do not use in a production enviroment until fully completed.

:: AUTO DETECT INSTALL ::

bash -c "$(wget -qO- https://raw.githubusercontent.com/DRM-Scripts/DRMPHP/master/install.sh)" && chmod +x install.sh

-- UBUNTU 18.04 --

cd /home && wget https://raw.githubusercontent.com/DRM-Scripts/DRMPHP/master/installer-beta.sh && chmod 777 ./installer-beta.sh && sed -i -e 's/\r$//' installer-beta.sh && ./installer-beta.sh

-- UBUNTU 20.04 --

cd /home && wget https://raw.githubusercontent.com/DRM-Scripts/DRMPHP/master/installer-beta_2004.sh && chmod 777 ./installer-beta_2004.sh && sed -i -e 's/\r$//' installer-beta_2004.sh && ./installer-beta_2004.sh

-- UBUNTU 22.04 --

cd /home && wget https://raw.githubusercontent.com/DRM-Scripts/DRMPHP/master/installer-beta_2204.sh && chmod 777 ./installer-beta_2204.sh && sed -i -e 's/\r$//' installer-beta_2204.sh && ./installer-beta_2204.sh
