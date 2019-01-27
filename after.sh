#!/bin/sh

# If you would like to do some extra provisioning you may
# add any commands you wish to this file and they will
# be run after the Homestead machine is provisioned.
#
# If you have user-specific configurations you would like
# to apply, you may also create user-customizations.sh,
# which will be run after this script.

sudo curl -LO https://deployer.org/deployer.phar
sudo mv deployer.phar /usr/local/bin/dep
sudo chmod +x /usr/local/bin/dep
sudo phpdismod xdebug
sudo service php7.3-fpm reload

# Wkhtmltopdf
if sudo [ ! -f /usr/bin/wkhtmltopdf.sh ]; then
    sudo apt-get -y install wkhtmltopdf libfontconfig xvfb
    sudo printf '#!/bin/bash\nxvfb-run -a --server-args="-screen 0, 1920x1080x24" /usr/bin/wkhtmltopdf -q $*' | sudo tee /usr/bin/wkhtmltopdf.sh
    sudo chmod a+x /usr/bin/wkhtmltopdf.sh
    sudo ln -s /usr/bin/wkhtmltopdf.sh /usr/local/bin/wkhtmltopdf
fi

# Wkhtmltoimage
if sudo [ ! -f /usr/bin/wkhtmltoimage.sh ]; then
    sudo apt-get -y install wkhtmltoimage libfontconfig xvfb
    sudo printf '#!/bin/bash\nxvfb-run -a --server-args="-screen 0, 1920x1080x24" /usr/bin/wkhtmltoimage -q $*' | sudo tee /usr/bin/wkhtmltoimage.sh
    sudo chmod a+x /usr/bin/wkhtmltoimage.sh
    sudo ln -s /usr/bin/wkhtmltoimage.sh /usr/local/bin/wkhtmltoimage
fi