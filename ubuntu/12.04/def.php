<?php
	header('Content-type: text/plain;');
		
	$autopart = (isset($_GET['autopart'])) ? $_GET['autopart'] : false;
	$desktop = (isset($_GET['desktop'])) ? $_GET['desktop'] : 0;
?>
#####################################################################
# There are a few settings that are expected to be set at runtime.
# 
# autopart - name of the disk to auto-partition, or leave blank to be prompted for the partitioner.  Eg: sda, vda, or false
# desktop - install desktop env.  true/false
# 
# Call the script like this: http://<?=$_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']?>?arch=i386&desktop=1 
#
# Call the Kickstart file and play with some different options, and review the output.  
# If you like what you see, add the URL to a new install disk or PXE boot menu.
#####################################################################

d-i debian-installer/locale string en_US

d-i debian-installer/language string en
d-i debian-installer/country string US
d-i debian-installer/locale string en_US.UTF-8

d-i console-setup/ask_detect boolean false
d-i keyboard-configuration/layoutcode string us

d-i netcfg/choose_interface select auto

d-i mirror/country string manual
d-i mirror/http/hostname string s10-deploy
d-i mirror/http/directory string /mirrors/ubuntu
d-i mirror/http/proxy string

d-i clock-setup/utc boolean true
d-i time/zone string US/Central
d-i clock-setup/ntp boolean true
d-i clock-setup/ntp-server string 1.north-america.pool.ntp.org

<?php if ($autopart): ?>
d-i partman-auto/disk string /dev/<?=$autopart?>

d-i partman-auto/method string lvm
d-i partman-lvm/device_remove_lvm boolean true
d-i partman-lvm/confirm boolean true
d-i partman-auto-lvm/guided_size string max
d-i partman-auto/choose_recipe select atomic
d-i partman-auto-lvm/new_vg_name string system

d-i partman-md/confirm boolean true
d-i partman-partitioning/confirm_write_new_label boolean true
d-i partman/choose_partition select finish
d-i partman/confirm boolean true
d-i partman/confirm_nooverwrite boolean true
d-i partman-lvm/confirm_nooverwrite boolean true
<?php endif; ?>

d-i passwd/root-login boolean true
d-i passwd/root-password password r00tme
d-i passwd/root-password-again password r00tme
#d-i passwd/root-password-crypted password (hash)

d-i passwd/user-fullname string Andrew Wells
d-i passwd/username string andrew
d-i passwd/user-password password insecure
d-i passwd/user-password-again password insecure
#d-i passwd/user-password-crypted password (hash)
d-i user-setup/allow-password-weak boolean true
d-i user-setup/encrypt-home boolean false

d-i apt-setup/restricted boolean true
d-i apt-setup/universe boolean true
d-i apt-setup/backports boolean true
d-i apt-setup/services-select multiselect security
d-i apt-setup/use_mirror boolean true
d-i apt-setup/security_host string s10-deploy
d-i apt-setup/security_path string /mirrors/ubuntu

# Additional repositories, local[0-9] available
d-i apt-setup/local0/repository string http://s10-deploy/mirrors/puppetlabs precise main dependencies
d-i apt-setup/local0/comment string Puppetlabs Repo
d-i apt-setup/local0/source boolean true
d-i apt-setup/local0/key string http://apt.puppetlabs.com/pubkey.gpg

<?php if ($desktop): ?>
tasksel tasksel/first multiselect xubuntu-desktop
<?php else: ?>
tasksel tasksel/first multiselect ubuntu-server
<?php endif; ?>

d-i pkgsel/include string ssh openssh-server htop iftop iotop curl puppet

d-i pkgsel/update-policy select unattended-upgrades

d-i pkgsel/language-packs multiselect en

d-i preseed/late_command string in-target sed -i s/START=no/START=yes/g /etc/default/puppet

d-i grub-installer/only_debian boolean true

d-i finish-install/reboot_in_progress note
#d-i debian-installer/exit/poweroff boolean true
d-i cdrom-detect/eject boolean false
