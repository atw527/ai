<?php
	header('Content-type: text/plain;');
	$arch = (isset($_GET['arch'])) ? $_GET['arch'] : 'x86_64';
	$hostname = (isset($_GET['hostname'])) ? $_GET['hostname'] : 'dev-centos';
	
	$mirror = 'http://ds04-deploy/mirrors';
?>
install

url --url=<?=$mirror?>/centos/6/os/<?=$arch?> 

lang en_US.UTF-8
keyboard us

network --onboot yes --device eth0 --bootproto dhcp --noipv6 --hostname <?=$hostname?> 

# Don't change the password here, but make note of it and change it after the OS is installed
rootpw r00tme

firewall --disabled
authconfig --enableshadow --passalgo=sha512

<?php if (isset($_GET['desktop']) && $_GET['desktop']): ?>
firstboot --enable
<?php endif; ?>

selinux --enforcing
timezone --utc America/Chicago

bootloader --location=mbr --driveorder=sda --append="crashkernel=auto rhgb quiet"
zerombr
clearpart --all --initlabel --drives=sda

part /boot --fstype=ext4 --size=500
part pv.008002 --grow --size=1

volgroup vg_system --pesize=4096 pv.008002
logvol / --fstype=ext4 --name=lv_root --vgname=vg_system --grow --size=1024 --maxsize=51200
logvol swap --name=lv_swap --vgname=vg_system --size=1024

reboot

%packages

@core
@server-policy

<?php if (isset($_GET['desktop']) && $_GET['desktop']): ?>
@base
@debugging
@basic-desktop
@desktop-debugging
@desktop-platform
@directory-client
@fonts
@general-desktop
@graphical-admin-tools
@input-methods
@internet-applications
@internet-browser
@java-platform
@legacy-x
@network-file-system-client
@office-suite
@print-client
@remote-desktop-clients
@server-platform
@server-policy
@x11
mtools
pax
oddjob
wodim
sgpio
genisoimage
device-mapper-persistent-data
abrt-gui
samba-winbind
certmonger
pam_krb5
krb5-workstation
gnome-pilot
libXmu
<?php endif; ?>

%end

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

%end
