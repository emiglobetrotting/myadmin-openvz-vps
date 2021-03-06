<?php

namespace Detain\MyAdminOpenvz;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class Plugin
 *
 * @package Detain\MyAdminOpenvz
 */
class Plugin {

	public static $name = 'OpenVZ VPS';
	public static $description = 'Allows selling of OpenVZ VPS Types. OpenVZ is a container-based virtualization for Linux. OpenVZ creates multiple secure, isolated Linux containers (otherwise known as VEs or VPSs) on a single physical server enabling better server utilization and ensuring that applications do not conflict. Each container performs and executes exactly like a stand-alone server; a container can be rebooted independently and have root access, users, IP addresses, memory, processes, files, applications, system libraries and configuration files.   More info at https://openvz.org/';
	public static $help = '';
	public static $module = 'vps';
	public static $type = 'service';

	/**
	 * Plugin constructor.
	 */
	public function __construct() {
	}

	/**
	 * @return array
	 */
	public static function getHooks() {
		return [
			self::$module.'.settings' => [__CLASS__, 'getSettings'],
			//self::$module.'.activate' => [__CLASS__, 'getActivate'],
			self::$module.'.deactivate' => [__CLASS__, 'getDeactivate'],
			self::$module.'.queue' => [__CLASS__, 'getQueue'],
		];
	}

	/**
	 * @param \Symfony\Component\EventDispatcher\GenericEvent $event
	 */
	public static function getActivate(GenericEvent $event) {
		$serviceClass = $event->getSubject();
		if (in_array($event['type'], [get_service_define('OPENVZ'), get_service_define('SSD_OPENVZ')])) {
			myadmin_log(self::$module, 'info', self::$name.' Activation', __LINE__, __FILE__);
			$event->stopPropagation();
		}
	}

	/**
	 * @param \Symfony\Component\EventDispatcher\GenericEvent $event
	 */
	public static function getDeactivate(GenericEvent $event) {
		if (in_array($event['type'], [get_service_define('OPENVZ'), get_service_define('SSD_OPENVZ')])) {
			myadmin_log(self::$module, 'info', self::$name.' Deactivation', __LINE__, __FILE__);
			$serviceClass = $event->getSubject();
			$GLOBALS['tf']->history->add(self::$module.'queue', $serviceClass->getId(), 'delete', '', $serviceClass->getCustid());
		}
	}

	/**
	 * @param \Symfony\Component\EventDispatcher\GenericEvent $event
	 */
	public static function getSettings(GenericEvent $event) {
		$settings = $event->getSubject();
		$settings->add_text_setting(self::$module, 'Slice Costs', 'vps_slice_ovz_cost', 'OpenVZ VPS Cost Per Slice:', 'OpenVZ VPS will cost this much for 1 slice.', $settings->get_setting('VPS_SLICE_OVZ_COST'));
		$settings->add_text_setting(self::$module, 'Slice Costs', 'vps_slice_ssd_ovz_cost', 'SSD OpenVZ VPS Cost Per Slice:', 'SSD OpenVZ VPS will cost this much for 1 slice.', $settings->get_setting('VPS_SLICE_SSD_OVZ_COST'));
		$settings->add_text_setting(self::$module, 'Slice OpenVZ Amounts', 'vps_slice_openvz_avnumproc', 'avnumproc', 'The average number of processes and threads. ', $settings->get_setting('VPS_SLICE_OPENVZ_AVNUMPROC'));
		$settings->add_text_setting(self::$module, 'Slice OpenVZ Amounts', 'vps_slice_openvz_numproc', 'numproc', 'The maximal number of processes and threads the VE may create. ', $settings->get_setting('VPS_SLICE_OPENVZ_NUMPROC'));
		$settings->add_text_setting(self::$module, 'Slice OpenVZ Amounts', 'vps_slice_openvz_numtcpsock', 'numtcpsock', 'The number of TCP sockets (PF_INET family, SOCK_STREAM type). This parameter limits the number of TCP connections and, thus, the number of clients the server application can handle in parallel. ', $settings->get_setting('VPS_SLICE_OPENVZ_NUMTCPSOCK'));
		$settings->add_text_setting(self::$module, 'Slice OpenVZ Amounts', 'vps_slice_openvz_numothersock', 'numothersock', ' The number of sockets other than TCP ones. Local (UNIX-domain) sockets are used for communications inside the system. UDP sockets are used, for example, for Domain Name Service (DNS) queries. UDP and other sockets may also be used in some very specialized applications (SNMP agents and others). ', $settings->get_setting('VPS_SLICE_OPENVZ_NUMOTHERSOCK'));
		$settings->add_text_setting(self::$module, 'Slice OpenVZ Amounts', 'vps_slice_openvz_cpuunits', 'cpuunits', '', $settings->get_setting('VPS_SLICE_OPENVZ_CPUUNITS'));
		$settings->add_text_setting(self::$module, 'Slice OpenVZ Amounts', 'vps_slice_openvz_cpus', 'slices per core', '', $settings->get_setting('VPS_SLICE_OPENVZ_CPUS'));
		$settings->add_text_setting(self::$module, 'Slice OpenVZ Amounts', 'vps_slice_openvz_dgramrcvbuf', 'dgramrcvbuf', 'The total size of receive buffers of UDP and other datagram protocols. ', $settings->get_setting('VPS_SLICE_OPENVZ_DGRAMRCVBUF'));
		$settings->add_text_setting(self::$module, 'Slice OpenVZ Amounts', 'vps_slice_openvz_tcprcvbuf', 'tcprcvbuf', 'The total size of receive buffers for TCP sockets, i.e. the amount of kernel memory allocated for the data received from the remote side, but not read by the local application yet. ', $settings->get_setting('VPS_SLICE_OPENVZ_TCPRCVBUF'));
		$settings->add_text_setting(self::$module, 'Slice OpenVZ Amounts', 'vps_slice_openvz_tcpsndbuf', 'tcpsndbuf', 'The total size of send buffers for TCP sockets, i.e. the amount of kernel memory allocated for the data sent from an application to a TCP socket, but not acknowledged by the remote side yet. ', $settings->get_setting('VPS_SLICE_OPENVZ_TCPSNDBUF'));
		$settings->add_text_setting(self::$module, 'Slice OpenVZ Amounts', 'vps_slice_openvz_othersockbuf', 'othersockbuf', 'The total size of UNIX-domain socket buffers, UDP, and other datagram protocol send buffers. ', $settings->get_setting('VPS_SLICE_OPENVZ_OTHERSOCKBUF'));
		$settings->add_text_setting(self::$module, 'Slice OpenVZ Amounts', 'vps_slice_openvz_numflock', 'numflock', 'The number of file locks created by all VE processes. ', $settings->get_setting('VPS_SLICE_OPENVZ_NUMFLOCK'));
		$settings->add_text_setting(self::$module, 'Slice OpenVZ Amounts', 'vps_slice_openvz_numpty_base', 'numpty_base', 'This setting is multiplied by the number of slices. This parameter is usually used to limit the number of simultaneous shell sessions.', $settings->get_setting('VPS_SLICE_OPENVZ_NUMPTY_BASE'));
		$settings->add_text_setting(self::$module, 'Slice OpenVZ Amounts', 'vps_slice_openvz_numpty', 'numpty', 'This parameter is usually used to limit the number of simultaneous shell sessions.', $settings->get_setting('VPS_SLICE_OPENVZ_NUMPTY'));
		$settings->add_text_setting(self::$module, 'Slice OpenVZ Amounts', 'vps_slice_openvz_shmpages', 'shmpages', 'The total size of shared memory (IPC, shared anonymous mappings and tmpfs objects). ', $settings->get_setting('VPS_SLICE_OPENVZ_SHMPAGES'));
		$settings->add_text_setting(self::$module, 'Slice OpenVZ Amounts', 'vps_slice_openvz_numiptent', 'numiptent', 'The number of IP packet filtering entries. ', $settings->get_setting('VPS_SLICE_OPENVZ_NUMIPTENT'));
		$settings->add_select_master(self::$module, 'Default Servers', self::$module, 'new_vps_openvz_server', 'OpenVZ NJ Server', NEW_VPS_OPENVZ_SERVER, 6, 1);
		$settings->add_select_master(self::$module, 'Default Servers', self::$module, 'new_vps_ssd_openvz_server', 'SSD OpenVZ NJ Server', NEW_VPS_SSD_OPENVZ_SERVER, 5, 1);
		$settings->add_select_master(self::$module, 'Default Servers', self::$module, 'new_vps_la_openvz_server', 'OpenVZ LA Server', NEW_VPS_LA_OPENVZ_SERVER, 6, 2);
		//$settings->add_select_master(self::$module, 'Default Servers', self::$module, 'new_vps_ny_openvz_server', 'OpenVZ NY4 Server', NEW_VPS_NY_OPENVZ_SERVER, 0, 3);
		$settings->add_dropdown_setting(self::$module, 'Out of Stock', 'outofstock_openvz', 'Out Of Stock OpenVZ Secaucus', 'Enable/Disable Sales Of This Type', $settings->get_setting('OUTOFSTOCK_OPENVZ'), ['0', '1'], ['No', 'Yes']);
		$settings->add_dropdown_setting(self::$module, 'Out of Stock', 'outofstock_ssd_openvz', 'Out Of Stock SSD OpenVZ Secaucus', 'Enable/Disable Sales Of This Type', $settings->get_setting('OUTOFSTOCK_SSD_OPENVZ'), ['0', '1'], ['No', 'Yes']);
		$settings->add_dropdown_setting(self::$module, 'Out of Stock', 'outofstock_openvz_la', 'Out Of Stock OpenVZ Los Angeles', 'Enable/Disable Sales Of This Type', $settings->get_setting('OUTOFSTOCK_OPENVZ_LA'), ['0', '1'], ['No', 'Yes']);
		$settings->add_dropdown_setting(self::$module, 'Out of Stock', 'outofstock_openvz_ny', 'Out Of Stock OpenVZ Equinix NY4', 'Enable/Disable Sales Of This Type', $settings->get_setting('OUTOFSTOCK_OPENVZ_NY'), ['0', '1'], ['No', 'Yes']);
		$settings->add_dropdown_setting(self::$module, 'Out of Stock', 'outofstock_ssd_openvz_ny', 'Out Of Stock SSD OpenVZ Equinix NY4', 'Enable/Disable Sales Of This Type', $settings->get_setting('OUTOFSTOCK_SSD_OPENVZ_NY'), ['0', '1'], ['No', 'Yes']);
	}

	/**
	 * @param \Symfony\Component\EventDispatcher\GenericEvent $event
	 */
	public static function getQueue(GenericEvent $event) {
		if (in_array($event['type'], [get_service_define('OPENVZ'), get_service_define('SSD_OPENVZ')])) {
			$vps = $event->getSubject();
			myadmin_log(self::$module, 'info', self::$name.' Queue '.ucwords(str_replace('_', ' ', $vps['action'])).' for VPS '.$vps['vps_hostname'].'(#'.$vps['vps_id'].'/'.$vps['vps_vzid'].')', __LINE__, __FILE__);
			$server_info = $vps['server_info'];
			if (!file_exists(__DIR__.'/../templates/'.$vps['action'].'.sh.tpl')) {
				myadmin_log(self::$module, 'error', 'Call '.$vps['action'].' for VPS '.$vps['vps_hostname'].'(#'.$vps['vps_id'].'/'.$vps['vps_vzid'].') Does not Exist for '.self::$name, __LINE__, __FILE__);
			} else {
				$smarty = new \TFSmarty();
				$smarty->assign($vps);
				echo $smarty->fetch(__DIR__.'/../templates/'.$vps['action'].'.sh.tpl');
				$event->stopPropagation();
			}
		}
	}
}