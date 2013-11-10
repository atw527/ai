<?php
	header('Content-type: text/plain;');
		
	$arch = (isset($_GET['arch'])) ? $_GET['arch'] : 'x86_64';

	$mirror = 'http://s10-deploy/mirrors';
?>
#####################################################################
# Only the arch is set at runtime.  Mirror is hard-coded above.
# 
# arch - i386 or x86_64
# 
# Call the script like this: http://<?=$_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']?>?arch=<?=$_GET['arch']?> 
#
# Call the Kickstart file and play with some different options, and review the output.  
# If you like what you see, add the URL to a new install disk or PXE boot menu.
#####################################################################

install

url --url=<?=$mirror?>/centos/6/os/<?=$arch?>

lang en_US.UTF-8
keyboard us

timezone --utc America/Chicago

%post --log=/root/post-install.log

# DHCP hostname fix
echo "DHCP_HOSTNAME=`hostname`" >> /etc/sysconfig/network

# set the mirror
sed -i s@#baseurl=http://mirror.centos.org@baseurl=<?=$mirror?>@g /etc/yum.repos.d/CentOS-Base.repo

cd /root
wget https://raw.github.com/atw527/dotfiles/master/.vimrc
cp .vimrc /etc/skel/

echo "PS1='[\[\e[01;31m\]\u@\h \w\[\e[0m\]]\$ '" >> /root/.bashrc
echo "PS1='[\u@\h \w]\$ '" >> /etc/skel/.bashrc

rpm --import /etc/pki/rpm-gpg/RPM-GPG-KEY-CentOS-6

%end
