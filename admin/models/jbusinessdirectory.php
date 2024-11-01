<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modeladmin');

use MVC\Installer\Installer;
use MVC\Installer\InstallerHelper;
use MVC\Factory;
use MVC\Filesystem\File;
use MVC\Router\Route;
use MVC\Language\Text;

require_once BD_HELPERS_PATH.'/installer.php';

/**
 * Class JBusinessDirectoryModelJBusinessDirectory
 */
class JBusinessDirectoryModelJBusinessDirectory extends JModelAdmin {
	/**
	 * @var array List of directory apps
	 * @since 5.0
	 */
	private $directoryApps = array(
		"wpbd-recurring-events",
        "wpbd-appointments",
        "wpbd-conference",
        "wpbd-event-appointments",
        "wpbd-event-bookings",
        "wpbd-sell-offers",
        "wpbd-stripe-subscription",
        "wpbd-stripe",
        "wpbd-paypal-subscription",
        "wpbd-payfast-subscription",
        "wpbd-campaigns",
        "wpbd-authorize",
        "wpbd-authorize-subscription",
        "wpbd-quote-requests",
        "wpbd-mercado-pago",
        "wpbd-elastic-search",
		"wpbd-trips",
		"wpbd-videos",
		"wpbd-mollie",
		"wpbd-mollie-subscription",
		"wpbd-cardlink",
		"wpbd-cardlink-subscription",
		"wpbd-razorpay",
	);

	/**
	 * @var array List of payment processor apps
	 * @since 5.0
	 */
	private $paymentApps = array(
		"wpbd-stripe-subscription",
		"wpbd-stripe",
		"wpbd-paypal-subscription",
		"wpbd-payfast-subscription",
		"wpbd-authorize",
		"wpbd-authorize-subscription",
		"wpbd-mercado-pago",
		"wpbd-mollie",
		"wpbd-mollie-subscription",
		"wpbd-cardlink",
		"wpbd-cardlink-subscription",
		"wpbd-razorpay",
	);

	/**
	 * @var array List of directory app names
	 * @since 5.0
	 */
	private $directoryAppNames = array(
		"WPBD Recurring Events",
        "WPBD Appointments",
        "WPBD Conference",
        "WPBD Event Appointments",
        "WPBD Event Bookings",
        "WPBD Sell Offers",
        "WPBD Stripe",
        "WPBD Stripe Subscriptions",
        "WPBD PayPal Subscriptions",
        "WPBD Payfast Subscriptions",
        "WPBD Campaigns",
        "WPBD Authorize",
        "WPBD Authorize Subscriptions",
        "WPBD Quote Requests",
        "WPBD Mercado Pago",
        "WPBD ElasticSearch",
		"WPBD Trips",
		"WPBD Videos",
		"WPBD Mollie",
		"WPBD Mollie Subscriptions",
		"WPBD CardLink",
		"WPBD CardLink Subscriptions",
		"WPBD Razorpay",
	);

	/**
	 * @var array List of directory extensions names
	 * @since 5.1.2
	 */
	private $directoryExtensionsNames = array(
	);

	/**
	 * @var array List of directory app paths
	 * @since 5.0
	 */
	private $directoryAppPaths = array(
		JPATH_COMPONENT_ADMINISTRATOR . '/views/event/tmpl/edit_recurring.php',
		JPATH_COMPONENT_ADMINISTRATOR . '/controllers/companyservice.php',
		JPATH_COMPONENT_ADMINISTRATOR . '/controllers/conference.php',
		JPATH_COMPONENT_ADMINISTRATOR . '/controllers/eventappointment.php',
		JPATH_COMPONENT_ADMINISTRATOR . '/controllers/eventreservation.php',
		JPATH_COMPONENT_ADMINISTRATOR . '/controllers/offerorder.php',
		JPATH_COMPONENT_SITE . '/classes/payment/processors/StripeProcessor.php',
		JPATH_COMPONENT_SITE . '/classes/payment/processors/StripeSubscriptions.php',
		JPATH_COMPONENT_SITE . '/classes/payment/processors/PaypalSubscriptions.php',
		JPATH_COMPONENT_SITE . '/classes/payment/processors/PayfastSubscriptions.php',
		JPATH_COMPONENT_ADMINISTRATOR . '/controllers/campaigns.php',
		JPATH_COMPONENT_SITE . '/classes/payment/processors/Authorize.php',
		JPATH_COMPONENT_SITE . '/classes/payment/processors/AuthorizeSubscriptions.php',
		JPATH_COMPONENT_ADMINISTRATOR . '/controllers/requestquotes.php',
		JPATH_COMPONENT_SITE . '/classes/payment/processors/MercadoPagoProcessor.php',
		JPATH_COMPONENT_SITE . '/classes/elasticsearch/JBusinessElasticHelper.php',
		JPATH_COMPONENT_SITE . '/controllers/trip.php',
		JPATH_COMPONENT_ADMINISTRATOR . '/controllers/video.php',
		JPATH_COMPONENT_SITE . '/classes/payment/processors/Mollie.php',
		JPATH_COMPONENT_SITE . '/classes/payment/processors/MollieSubscriptions.php',
		JPATH_COMPONENT_SITE . '/classes/payment/processors/Cardlink.php',
		JPATH_COMPONENT_SITE . '/classes/payment/processors/CardlinkSubscriptions.php',
		JPATH_COMPONENT_SITE . '/classes/payment/processors/Razorpay.php',
	);

	/**
	 * @var array List of directory extensions paths
	 * @since 5.1.2
	 */
	private $directoryExtensionsPaths = array(
		JPATH_SITE . '/modules/mod_jcategorybanners',
		JPATH_SITE . '/plugins/content/business',
		JPATH_SITE . '/modules/mod_jbusiness_maps',
		JPATH_SITE . '/modules/mod_jbusiness_locations',
		JPATH_SITE . '/modules/mod_jbusiness_reviews',
		JPATH_SITE . '/plugins/finder/offers',
		JPATH_SITE . '/plugins/finder/events',
		JPATH_SITE . '/plugins/finder/business',
		JPATH_SITE . '/plugins/search/jbdoffers',
		JPATH_SITE . '/plugins/search/jbdevents',
		JPATH_SITE . '/plugins/search/jbdbusiness',
	);

	private $appsInstallStatuses = array();

	/**
	 * @var array Application Settings
	 * @since 4.9.0
	 */
	public $appSettings;

	/**
	 * JBusinessDirectoryModelJBusinessDirectory constructor.
	 *
	 * @param array $config
	 *
	 * @since 4.9.0
	 */
	public function __construct(array $config = array()) {
		$this->appSettings = JBusinessUtil::getApplicationSettings();

		parent::__construct($config);
	}

	public function getForm($data = array(), $loadData = true) {
	}

	public function getStatistics() {
		$statistics = new stdClass();

		$companyTable                   = JTable::getInstance('Company', 'JTable');
		$statistics->totalListings      = $companyTable->getTotalListings();
		$statistics->today              = $companyTable->getTodayListings();
		$statistics->week               = $companyTable->getWeekListings();
		$statistics->month              = $companyTable->getMonthListings();
		$statistics->year               = $companyTable->getYearListings();
		$statistics->listingsTotalViews = $companyTable->getListingsViews();

		$categoryTable               = JTable::getInstance('Category', 'JBusinessTable');
		$statistics->totalCategories = (int)$categoryTable->getTotalCategories();

		$offersTable                  = JTable::getInstance('Offer', 'JTable');
		$statistics->totalOffers      = (int)$offersTable->getTotalNumberOfOffers();
		$statistics->activeOffers     = (int)$offersTable->getTotalActiveOffers();
		$statistics->offersTotalViews = (int)$offersTable->getOffersViews();

		$eventsTable                  = JTable::getInstance('Event', 'JTable');
		$statistics->totalEvents      = (int)$eventsTable->getTotalNumberOfEvents();
		$statistics->activeEvents     = (int)$eventsTable->getTotalActiveEvents();
		$statistics->eventsTotalViews = (int)$eventsTable->getEventsViews();

		$statistics->totalViews = (int)$statistics->listingsTotalViews + (int)$statistics->offersTotalViews + (int)$statistics->eventsTotalViews;

		return $statistics;
	}

	/**
	 * Get the income for different time periods.
	 */
	public function getIncome() {
		$income = new stdClass();

		$orderTable    = JTable::getInstance('Order', 'JTable');
		$income->total = $orderTable->getTotalIncome();
		$income->today = $orderTable->getTodayIncome();
		$income->week  = $orderTable->getWeekIncome();
		$income->month = $orderTable->getMonthIncome();
		$income->year  = $orderTable->getYearIncome();

		return $income;
	}

	public function getNewCompanies() {
		$jinput = JFactory::getApplication()->input;

		$start_date = $jinput->get('start_date');
		$start_date = date("Y-m-d", strtotime($start_date));
		$end_date   = $jinput->get('end_date');
		$end_date   = date("Y-m-d", strtotime($end_date));

		$companyTable = JTable::getInstance('Company', 'JTable');
		$result       = $companyTable->getNewCompanies($start_date, $end_date);

		if (!empty($result)) {
			//add start date element if it does not exists
			if ($result[0]->date != $start_date) {
				$item        = new stdClass();
				$item->date  = $start_date;
				$item->value = 0;
				array_unshift($result, $item);
			}

			//add end date element if it does not exists
			if (end($result)->date != $end_date) {
				$item        = new stdClass();
				$item->date  = $end_date;
				$item->value = 0;
				array_push($result, $item);
			}
		} else {
			$firstItem        = new stdClass();
			$firstItem->date  = $start_date;
			$firstItem->value = 0;
			array_unshift($result, $firstItem);

			$endItem        = new stdClass();
			$endItem->date  = $end_date;
			$endItem->value = 0;
			array_push($result, $endItem);
		}

		return $result;
	}

	public function getNewOffers() {
		$jinput = JFactory::getApplication()->input;

		$start_date = $jinput->get('start_date');
		$start_date = date("Y-m-d", strtotime($start_date));
		$end_date   = $jinput->get('end_date');
		$end_date   = date("Y-m-d", strtotime($end_date));

		$offerTable = JTable::getInstance('Offer', 'JTable');
		$result     = $offerTable->getNewOffers($start_date, $end_date);

		if (!empty($result)) {
			//add start date element if it does not exists
			if ($result[0]->date != $start_date) {
				$item        = new stdClass();
				$item->date  = $start_date;
				$item->value = 0;
				array_unshift($result, $item);
			}

			//add end date element if it does not exists
			if (end($result)->date != $end_date) {
				$item        = new stdClass();
				$item->date  = $end_date;
				$item->value = 0;
				array_push($result, $item);
			}
		} else {
			$firstItem        = new stdClass();
			$firstItem->date  = $start_date;
			$firstItem->value = 0;
			array_unshift($result, $firstItem);

			$endItem        = new stdClass();
			$endItem->date  = $end_date;
			$endItem->value = 0;
			array_push($result, $endItem);
		}

		return $result;
	}

	public function getNewEvents() {
		$jinput = JFactory::getApplication()->input;

		$start_date = $jinput->get('start_date');
		$start_date = date("Y-m-d", strtotime($start_date));
		$end_date   = $jinput->get('end_date');
		$end_date   = date("Y-m-d", strtotime($end_date));

		$eventTable = JTable::getInstance('Event', 'JTable');
		$result     = $eventTable->getNewEvents($start_date, $end_date);

		if (!empty($result)) {
			//add start date element if it does not exists
			if ($result[0]->date != $start_date) {
				$item        = new stdClass();
				$item->date  = $start_date;
				$item->value = 0;
				array_unshift($result, $item);
			}

			//add end date element if it does not exists
			if (end($result)->date != $end_date) {
				$item        = new stdClass();
				$item->date  = $end_date;
				$item->value = 0;
				array_push($result, $item);
			}
		} else {
			$firstItem        = new stdClass();
			$firstItem->date  = $start_date;
			$firstItem->value = 0;
			array_unshift($result, $firstItem);

			$endItem        = new stdClass();
			$endItem->date  = $end_date;
			$endItem->value = 0;
			array_push($result, $endItem);
		}

		return $result;
	}

	public function getNewIncome() {
		$jinput = JFactory::getApplication()->input;

		$start_date = $jinput->get('start_date');
		$start_date = date("Y-m-d", strtotime($start_date));
		$end_date   = $jinput->get('end_date');
		$end_date   = date("Y-m-d", strtotime($end_date));

		$incomeTable = JTable::getInstance('Order', 'JTable');
		$income      = $incomeTable->getNewIncome($start_date, $end_date);

		return $income;
	}

	/**
	 *
	 */
	public function getServerNews() {
		$rss = new DOMDocument();
		$rss->load('https://www.cmsjunkie.com/news/rss/');

		$feeds = array();
		foreach ($rss->getElementsByTagName('item') as $node) {
			$item = array(
				'title'        => $node->getElementsByTagName('title')->item(0)->nodeValue,
				'link'         => $node->getElementsByTagName('link')->item(0)->nodeValue,
				'description'  => $node->getElementsByTagName('description')->item(0)->nodeValue,
				'publish_date' => $node->getElementsByTagName('pubDate')->item(0)->nodeValue
			);
			array_push($feeds, $item);
		}
		return $feeds;
	}

	/**
	 * Get the latest news from local database and prepare them
	 *
	 * @param unknown_type $limit
	 */
	public function getLocalNews($limit = 3) {
		// $limit -> the limit of the news to be displayed in the dashboard
		$db    = JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_news order by publish_date desc limit $limit";
		$db->setQuery($query);
		$news = $db->loadObjectList();

		foreach ($news as $item) {
			$publish_ago       = JBusinessUtil::convertTimestampToAgo($item->publish_date);
			$item->publish_ago = $publish_ago;
			$item->new         = false;
			// $time          = strftime('%Y-%m-%d', (strtotime('7 days ago')));
			$date = JFactory::getDate(strtotime('7 days ago'));
			$time = $date->format('Y-m-d');
			// $retrieve_date = strftime('%Y-%m-%d', (strtotime($item->retrieve_date)));
			$retrieve_data = JFactory::getDate(strtotime($item->retrieve_date));
			$retrieve_date = $retrieve_data->format('Y-m-d');
			//For 7 days from the moment of the retrieve_date the news will be displayed like NEW
			if ($time < $retrieve_date) {
				$item->new = true;
			}

			$item->description  = mb_strimwidth(strip_tags($item->description), 0, 200, '...');
			$item->publishDateS = date('l, M d, Y', strtotime($item->publish_date));
		}

		return $news;
	}

	/**
	 * Retrieve the last news from database
	 *
	 */
	public function getLocalLastNews() {
		$db    = JFactory::getDBO();
		$query = "SELECT * FROM #__jbusinessdirectory_news ORDER BY retrieve_date DESC LIMIT 1";
		$db->setQuery($query);
		$lastNews = $db->loadObject();
		return $lastNews;
	}

	/**
	 * Get the latest news from server and store the new ones
	 *
	 */
	public function getLatestServerNews() {
		$lastNews = $this->getLocalLastNews();

		if (empty($lastNews)) {
			$serverNews = $this->getServerNews();
			$this->storeNews($serverNews);
		} else {
			$days_ago             = NEWS_REFRESH_PERIOD; // refresh records after specified days
			$check_date           = date('Y-m-d H:i:s', (strtotime($days_ago . ' days ago')));
			$lastNewsRetrieveDate = date('Y-m-d H:i:s', strtotime($lastNews->retrieve_date));

			if ($check_date > $lastNewsRetrieveDate) {
				$serverNews = $this->getServerNews();
				$localNews  = $this->getLocalNews();

				$feeds = array();
				foreach ($serverNews as $singleServerNews) {
					$title        = str_replace(' & ', ' &amp; ', $singleServerNews['title']);
					$link         = $singleServerNews['link'];
					$description  = $singleServerNews['description'];
					$publish_date = date('Y-m-d H:i:s', strtotime($singleServerNews['publish_date']));

					$flag = true;
					foreach ($localNews as $singleLocalNews) {
						$singleLocalNews_publish_date = date('Y-m-d H:i:s', strtotime($singleLocalNews->publish_date));
						if ($publish_date == $singleLocalNews_publish_date) {
							$flag = false;
						}
					}

					if ($flag) {
						$item = array(
							'title'        => $title,
							'link'         => $link,
							'description'  => $description,
							'publish_date' => $publish_date
						);
						array_push($feeds, $item);
					}
				}

				//if there are new news store them
				if (!empty($feeds)) {
					$this->storeNews($feeds);
					return $this->getLocalNews(3);
				}
			}
		}
	}

	/**
	 * Store the news into database
	 *
	 * @param unknown_type $feeds
	 */
	public function storeNews($feeds) {
		foreach ($feeds as $feed) {
			$title         = str_replace(' & ', ' &amp; ', $feed['title']);
			$link          = $feed['link'];
			$description   = $feed['description'];
			$publish_date  = date('Y-m-d H:i:s', strtotime($feed['publish_date']));
			$retrieve_date = date('Y-m-d H:i:s');

			$item                = new stdClass();
			$item->title         = $title;
			$item->link          = $link;
			$item->description   = $description;
			$item->publish_date  = $publish_date;
			$item->retrieve_date = $retrieve_date;

			$result = JFactory::getDbo()->insertObject('#__jbusinessdirectory_news', $item);
		}
	}

	/**
	 * Checks the Google Map API status (whether it's set or not), creates an object with
	 * all the relevant information (including the status) and returns it.
	 *
	 * @return stdClass
	 * @since 4.9.0
	 */
	public function getMapAPIStatus() {
		$action         = new stdClass();
		$action->status = !empty($this->appSettings->google_map_key) ? 1 : 0;
		if ($action->status) {
			$action->text = JText::_('LNG_GOOGLE_MAP_API_SET');
		} else {
			$action->text = JText::_('LNG_GOOGLE_MAP_API_NOT_SET');
		}

		$action->link = JRoute::_('index.php?option=com_jbusinessdirectory&view=applicationsettings');

		return $action;
	}

	/**
	 * Checks the SEO status. If SEO is set to yes, then it checks if the url translator plugin
	 * has been well configured. If yes, then the resulting status will be 1, otherwise the
	 * status will be set to 0. If the SEO has not been set at all, the status will remain 1.
	 *
	 * Prepares an object with all the relevant information for each case and returns it.
	 *
	 * @return stdClass
	 * @since 4.9.0
	 */
	public function getSEOStatus() {
		$action = new stdClass();
		
		return $action;
	}

	/**
	 * Checks if front-end ACL setting has been set to yes on application settings. If so,
	 * it makes sure that at least one of the permissions has been set, otherwise the status
	 * will be set to 0. If the front-end ACL setting is set to no in the first place, the
	 * status will remain 1.
	 *
	 * Prepares an object with all the relevant information for each case and returns it.
	 *
	 * @return stdClass
	 * @since 4.9.0
	 */
	public function getACLStatus() {
		$action = new stdClass();
		if (!$this->appSettings->front_end_acl) {
			$action->text   = JText::_('LNG_FRONT_ACL_NOT_ACTIVE');
			$action->status = 1; // if acl is not set, then it's OK
			$action->link   = JRoute::_('index.php?option=com_jbusinessdirectory&view=applicationsettings');
		} else {
			$permissionStatus = 0;

			// fetch the action rules for JBD
			$db    = JFactory::getDbo();
			$query = "SELECT rules FROM #__assets WHERE name='com_jbusinessdirectory'";
			$db->setQuery($query);
			$rules = $db->loadObject()->rules;
			$rules = json_decode($rules);

			// check to see if the rules are associated to at least one user group (excluding super user)
			if (isset($rules->{'directory.access.directory.management'})) {
				foreach ($rules->{'directory.access.directory.management'} as $key => $val) {
					if ($key != 8) {
						$permissionStatus = 1;
					}
				}
			}

			if (isset($rules->{'directory.access.listings'})) {
				foreach ($rules->{'directory.access.listings'} as $key => $val) {
					if ($key != 8) {
						$permissionStatus = 1;
					}
				}
			}

			if (!$permissionStatus) {
				$action->text   = JText::_('LNG_CHECK_PERMISSIONS');
				$action->link   = JRoute::_('index.php?option=com_config&view=component&component=com_jbusinessdirectory');
				$action->status = 0;
			} else {
				$action->text   = JText::_('LNG_ACL_AND_PERMISSIONS_SET');
				$action->link   = JRoute::_('index.php?option=com_jbusinessdirectory&view=applicationsettings');
				$action->status = 1;
			}
		}

		return $action;
	}

	/**
	 * Checks if CAPTCHA has been set to yes on application settings. If so, it the captcha
	 * has been defined, the status is set to 1. Otherwise, the status is set to 0.
	 * If CAPTCHA is set to no on application settings, the status remains 1.
	 *
	 * Prepares an object with all the relevant information for each case and returns it.
	 *
	 * @return stdClass
	 * @since 4.9.0
	 */
	public function getCaptchaStatus() {
		$action = new stdClass();
		if (!$this->appSettings->captcha) {
			$action->text   = JText::_('LNG_CAPTCHA_NOT_ACTIVE');
			$action->status = 1; // if CAPTCHA is not set, then it's OK
			$action->link   = JRoute::_('index.php?option=com_jbusinessdirectory&view=applicationsettings');
		} else {
			$captcha       = JFactory::getConfig()->get('captcha');
			$captchaStatus = !empty($captcha) ? 1 : 0;

			if (!$captchaStatus) {
				$action->text   = JText::_('LNG_CAPTCHA_NOT_SET');
				$action->link   = JRoute::_('index.php?option=com_config');
				$action->status = 0;
			} else {
				$action->text   = JText::_('LNG_CAPTCHA_SET');
				$action->link   = JRoute::_('index.php?option=com_jbusinessdirectory&view=applicationsettings');
				$action->status = 1;
			}
		}

		return $action;
	}

	public function getStatisticsStatus() {
		$action = new stdClass();
		$action->text   = JText::_('LNG_CHECK_STATISTICS');
		$action->status = 0;
		$action->link   = JRoute::_('index.php?option=com_jbusinessdirectory&view=statistics');

		return $action;

	}

	public function getSearchLogsStatus() {
		$action = new stdClass();
		$action->text   = JText::_('LNG_CHECK_SEARCH_LOGS');
		$action->status = 0; 
		$action->link   = JRoute::_('index.php?option=com_jbusinessdirectory&view=searchlogs');

		return $action;
	}


	/**
	 * Method that prepares all basic actions that need to be performed by the site manager.
	 * Each action has it's subject, status and link to the page where you may change the
	 * current status
	 *
	 * @return array
	 * @since 4.9.0
	 */
	public function getActions() {
		$actions = array();

		// GOOGLE MAP API
		$action    = $this->getMapAPIStatus();
		$actions[] = $action;

		// // SEO and Translator plugin
		// $action    = $this->getSEOStatus();
		// $actions[] = $action;

		// // ACL
		// $action    = $this->getACLStatus();
		// $actions[] = $action;

		// CAPTCHA
		$action    = $this->getCaptchaStatus();
		$actions[] = $action;
		
		// Statistics
		$statisticsTable = JTable::getInstance("statistics", "JTable");
		$numberOfStatistics = $statisticsTable->getTotalNumberOfStatistics();
		if ($numberOfStatistics > 10000){
			$action    = $this->getStatisticsStatus();
			$actions[] = $action;
		}

		// Search Logs
		$searchLogsTable = JTable::getInstance("searchlog", "JTable");
		$numberOfSearchLogs = $searchLogsTable->getTotalNumberOfSearchLogs();
		if ($numberOfSearchLogs > 10000){
		$action    = $this->getSearchLogsStatus();
		$actions[] = $action;
		}

		return $actions;
	}

	/**
	 * Retrieves all directory apps
	 *
	 * @return mixed
	 *
	 * @since 5.0
	 */
	public function getDirectoryApps() {
		$table = $this->getTable('DirectoryApps', 'JTable');
		$apps  = $table->getDirectoryApps(TYPE_DIRECTORY_APP);

		return $apps;
	}

	/**
	 * Retrieves all directory extensions
	 *
	 * @return mixed
	 *
	 * @since 5.1.2
	 */
	public function getDirectoryExtensions() {
		$table = $this->getTable('DirectoryApps', 'JTable');
		$apps  = $table->getDirectoryApps(TYPE_DIRECTORY_EXTENSION);

		return $apps;
	}

	/**
	 * Retrieves the uploaded package and prepares it for installation
	 *
	 * @param $file array uploaded file
	 *
	 * @since 5.0
	 * @throws Exception
	 */
	public function getAppPackage($file) {

		//dump("upload file");
		$userfile    = $file;
		$packageName = $userfile['name'];

		$upload_dir = wp_upload_dir();
		$user_dirname = $upload_dir['basedir'] . '/wp-businessdirectory/tmp';
		if (!file_exists($user_dirname)) {
			wp_mkdir_p($user_dirname);
		}

		$filename = wp_unique_filename($user_dirname, $file['name']);
		$filePath = $user_dirname . '/' . $filename;
		move_uploaded_file($userfile['tmp_name'], $filePath);

		require_once(ABSPATH . '/wp-admin/includes/file.php');
		WP_Filesystem();

		$dest = $user_dirname."/".substr($packageName, 0, strpos($packageName,"."));
		
		if (!file_exists($dest)) {
			wp_mkdir_p($dest);
		}
		unzip_file($filePath, $dest);

		$package = array();
		
		$package["dir"] = $dest;

		if ($this->isFound($this->directoryApps, $packageName)) {
			$package["name"] = $packageName;
		}

		return $package;
	}

	/**
	 * Processes file input and installs each of the packages individually
	 *
	 * @since  5.4.0
	 */
	public function bulkInstallApps() {
		// Get the uploaded file information.
		$input = Factory::getApplication()->input;

		$files = $input->files->get('file', null, 'raw');

		$results = array();
		foreach ($files as $file) {
			try {
				$result = $this->installApp($file);

				if ($result) {
					$tmp = new stdClass();
					$tmp->file = $file;
					$tmp->message = Text::_('LNG_DIRECTORY_APP_INSTALLED_SUCCESSFULLY');
					$tmp->status = 1;

					$results[] = $tmp;
				}
			} catch (Exception $e) {
				$tmp = new stdClass();
				$tmp->file = $file;
				$tmp->message = $e->getMessage();
				$tmp->status = 0;

				$results[] = $tmp;
			}
		}

		return $results;
	}

	/**
	 * Installs the uploaded package if it is a proper Joomla Extension and a valid Directory App.
	 *
	 * @param $file array uploaded  file
	 *
	 * @since 5.0
	 *
	 * @throws Exception
	 */
	public function installApp($file) {
		try {
			$package = $this->getAppPackage($file);
		} catch (Exception $e) {
			throw (new Exception($e->getMessage()));
		}

		if (isset($package["name"])) {
			return $this->installDirectoryApp($package);
		}
		
		return $result;
	}

	/**
	 * Method that installs the directory applications
	 *
	 * @param $package array
	 *
	 * @return bool
	 * @throws Exception
	 *
	 * @since 5.0
	 */
	public function installDirectoryApp($package) {
		if (!isset($package["dir"])) {
			throw (new Exception(Text::_('LNG_BAD_PACKAGE')));
		}

		$adminPath      = $package["dir"] . DS . "admin";
		$sitePath       = $package["dir"] . DS . "site";
		$extensionsPath = $package["dir"] . DS . "extensions";
		$this->appsInstallStatuses = $this->getAppStatuses();

		if (!file_exists($adminPath) && !file_exists($sitePath)) {
			throw (new Exception(Text::_('LNG_BAD_PACKAGE')));
		}

		if (file_exists($extensionsPath)) {
			//$this->installExtensions($extensionsPath);
		}

		$adminDestination = JPATH_COMPONENT_ADMINISTRATOR ;
		$siteDestination  = JPATH_COMPONENT_SITE;
		//dump($adminDestination);
		//dump($siteDestination);

		if (!file_exists($adminDestination) || !file_exists($siteDestination)) {
			throw (new Exception(Text::_('LNG_DESTINATION_DOESNT_EXIST')));
		}


		if (!$this->isFound($this->paymentApps, $package["name"])) {
			$this->copyToDestination($adminPath, $adminDestination);
		}

		if (file_exists($sitePath)) {
			$this->copyToDestination($sitePath, $siteDestination);
		}

		$manifest = $this->getDirectoryAppManifest($package["dir"]);

		if ($this->isFound($this->paymentApps, $package["name"])) {
			$this->addPaymentProcessor($manifest->name);
		}

		//InstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);
		Factory::getApplication()->enqueueMessage(Text::_('LNG_DIRECTORY_APP_INSTALLED_SUCCESSFULLY'));

		if (!$this->saveApp((string)$manifest->name,(string) $manifest->version)) {
			return false;
		}

		return true;
	}

	/**
	 * Install all extensions in a certain directory
	 *
	 * @param $extensionsPath string path of the extensions dir
	 *
	 * @since 5.0
	 */
	public function installExtensions($extensionsPath) {
		$extensionsDirs = JFolder::folders($extensionsPath);

		foreach ($extensionsDirs as $extensionDir) {
			$tmpInstaller = new JInstaller();
			$tmpInstaller->setOverwrite(true);
			if (!$tmpInstaller->install($extensionsPath . '/' . $extensionDir)) {
				JError::raiseWarning(100, "Extension :" . $extensionDir);
			}
		}
	}

	/**
	 * Adds a payment processor entry
	 *
	 * @param $processorName string name of the payment processor
	 *
	 * @since 5.0
	 */
	public function addPaymentProcessor($processorName) {
		$name = '';
		$type = '';
		$processorFields = array();
		$new = false;

		if (strcmp($processorName, "WPBD PayPal Subscriptions") == 0) {
			$name               = "Paypal Subscriptions";
			$type               = "paypalsubscriptions";
			$field[0]           = "paypal_email";
			$field[1]           = "test@domain.com";
			$field[2]           = "0";
			$processorFields[]  = $field;
			$field[0]           = "paypal_email";
			$field[1]           = "live@domain.com";
			$field[2]           = "1";
			$processorFields[]  = $field;
			if (!$this->appsInstallStatuses[JBD_APP_PAYPAL_SUBSCRIPTIONS]) {
				$new = true;
			}
		} elseif (strcmp($processorName, "WPBD Stripe") == 0) {
			$name               = "Stripe";
			$type               = "stripeprocessor";
			$field[0]           = "secret_key";
			$field[1]           = "please replace this with your own test key";
			$field[2]           = "0";
			$processorFields[]  = $field;
			$field[0]           = "publishable_key";
			$field[1]           = "please replace this with your own test key";
			$field[2]           = "0";
			$processorFields[]  = $field;
			$field[0]           = "secret_key";
			$field[1]           = "please replace this with your own key";
			$field[2]           = "1";
			$processorFields[]  = $field;
			$field[0]           = "publishable_key";
			$field[1]           = "please replace this with your own key";
			$field[2]           = "1";
			$processorFields[]  = $field;
			if (!$this->appsInstallStatuses[JBD_APP_STRIPE]) {
				$new = true;
			}
		} elseif (strcmp($processorName, "WPBD Stripe Subscriptions") == 0) {
			$name               = "Stripe Subscriptions";
			$type               = "stripesubscriptions";
			$field[0]           = "secret_key";
			$field[1]           = "please replace this with your own test key";
			$field[2]           = "0";
			$processorFields[]  = $field;
			$field[0]           = "publishable_key";
			$field[1]           = "please replace this with your own test key";
			$field[2]           = "0";
			$processorFields[]  = $field;
			$field[0]           = "signing_secret";
			$field[1]           = "please replace this with your own webhook signing secret";
			$field[2]           = "0";
			$processorFields[]  = $field;
			$field[0]           = "secret_key";
			$field[1]           = "please replace this with your own key";
			$field[2]           = "1";
			$processorFields[]  = $field;
			$field[0]           = "publishable_key";
			$field[1]           = "please replace this with your own key";
			$field[2]           = "1";
			$processorFields[]  = $field;
			$field[0]           = "signing_secret";
			$field[1]           = "please replace this with your own webhook signing secret";
			$field[2]           = "1";
			$processorFields[]  = $field;
			if (!$this->appsInstallStatuses[JBD_APP_STRIPE_SUBSCRIPTIONS]) {
				$new = true;
			}
		} elseif (strcmp($processorName, "WPBD Payfast Subscriptions") == 0) {
			$name               = "Payfast Subscriptions";
			$type               = "payfastsubscriptions";
			$field[0]           = "merchant_id";
			$field[1]           = "please replace this with your own test merchant id";
			$field[2]           = "0";
			$processorFields[]  = $field;
			$field[0]           = "merchant_key";
			$field[1]           = "please replace this with your own test merchant key";
			$field[2]           = "0";
			$processorFields[]  = $field;
			$field[0]           = "passphrase";
			$field[1]           = "please replace this with your own test passphrase";
			$field[2]           = "0";
			$processorFields[]  = $field;
			$field[0]           = "merchant_id";
			$field[1]           = "please replace this with your own merchant id";
			$field[2]           = "1";
			$processorFields[]  = $field;
			$field[0]           = "merchant_key";
			$field[1]           = "please replace this with your own merchant key";
			$field[2]           = "1";
			$processorFields[]  = $field;
			$field[0]           = "passphrase";
			$field[1]           = "please replace this with your own passphrase";
			$field[2]           = "1";
			$processorFields[]  = $field;
			if (!$this->appsInstallStatuses[JBD_APP_PAYFAST_SUBSCRIPTIONS]) {
				$new = true;
			}
		} elseif (strcmp($processorName, "WPBD Authorize") == 0) {
			$name               = "Authorize";
			$type               = "authorize";
			$field[0]           = "transaction_key";
			$field[1]           = "please replace this with your own test key";
			$field[2]           = "0";
			$processorFields[]  = $field;
			$field[0]           = "api_login_id";
			$field[1]           = "please replace this with your own test key";
			$field[2]           = "0";
			$processorFields[]  = $field;
			$field[0]           = "transaction_key";
			$field[1]           = "please replace this with your own key";
			$field[2]           = "1";
			$processorFields[]  = $field;
			$field[0]           = "api_login_id";
			$field[1]           = "please replace this with your own key";
			$field[2]           = "1";
			$processorFields[]  = $field;
			if (!$this->appsInstallStatuses[JBD_APP_AUTHORIZE]) {
				$new = true;
			}
		} elseif (strcmp($processorName, "WPBD Authorize Subscriptions") == 0) {
			$name               = "Authorize Subscriptions";
			$type               = "authorizesubscriptions";
			$field[0]           = "transaction_key";
			$field[1]           = "please replace this with your own test key";
			$field[2]           = "0";
			$processorFields[]  = $field;
			$field[0]           = "api_login_id";
			$field[1]           = "please replace this with your own test key";
			$field[2]           = "0";
			$processorFields[]  = $field;
			$field[0]           = "transaction_key";
			$field[1]           = "please replace this with your own key";
			$field[2]           = "1";
			$processorFields[]  = $field;
			$field[0]           = "api_login_id";
			$field[1]           = "please replace this with your own key";
			$field[2]           = "1";
			$processorFields[]  = $field;
			if (!$this->appsInstallStatuses[JBD_APP_AUTHORIZE_SUBSCRIPTIONS]) {
				$new = true;
			}
		} elseif (strcmp($processorName, "WPBD Mercado Pago") == 0) {
			$name = "Mercado Pago";
			$type = "mercadopagoprocessor";
			$field[0] = "public_key";
			$field[1] = "please replace this with your own test key";
			$field[2] = "0";
			$processorFields[] = $field;
			$field[0] = "access_token";
			$field[1] = "please replace this with your own test access token";
			$field[2] = "0";
			$processorFields[] = $field;
			$field[0] = "public_key";
			$field[1] = "please replace this with your own key";
			$field[2] = "1";
			$processorFields[] = $field;
			$field[0] = "access_token";
			$field[1] = "please replace this with your own token";
			$field[2] = "1";
			$processorFields[] = $field;
			if (!$this->appsInstallStatuses[JBD_APP_MERCADO_PAGO]) {
				$new = true;
			}
		}

		if ($new) {
			$db     = JFactory::getDBO();
			$query  = "INSERT INTO `#__jbusinessdirectory_payment_processors` (`name`, `type`, `mode`, `timeout`, `status`, `ordering`, `displayfront`) 
					VALUES ('$name', '$type', 'test', 10, 1, NULL, 1);";
			$db->setQuery($query);
			$db->execute();

			$id     = $db->insertid();
			$query  = "INSERT INTO `#__jbusinessdirectory_payment_processor_fields` (`column_name`, `column_value`, `column_mode`, `processor_id`) 
				        VALUES";

			for ($i = 0; $i < count($processorFields); $i++) {
				$query .= "('" . $processorFields[$i][0] . "','" . $processorFields[$i][1] . "','" . $processorFields[$i][2] . "'," . $id . ")";
				if ($i < count($processorFields) - 1) {
					$query .= ",";
				}
			}

			$db->setQuery($query);
			$db->execute();
		}
	}

	/**
	 * Recursive. Copies all source folders & files to destination path.
	 *
	 * @param $src string source path
	 * @param $dst string destination path
	 *
	 * @since 5.0
	 */
	public function copyToDestination($src, $dst) {
		$dir = opendir($src);
		if (!is_dir($dst)) {
			mkdir($dst, 0777);
		}

		while (($file = readdir($dir)) !== false) {
			if (($file != '.') && ($file != '..') && ($file != '.svn')) {
				if (is_dir($src . '/' . $file)) {
					$this->copyToDestination($src . DS . $file, $dst . DS . $file);
				} else {
					copy($src . DS . $file, $dst . DS . $file);
				}
			}
		}

		closedir($dir);
	}

	/**
	 * Method that reads and retrieves the manifest of a directory app
	 *
	 * @param $p_dir string path to package dir
	 *
	 * @return null|SimpleXMLElement
	 *
	 * @since 5.0.0
	 */
	public function getDirectoryAppManifest($p_dir) {
		\JLoader::import('joomla.filesystem.folder');

		$file = \JFolder::files($p_dir, '\.xml$', 1, true);

		$xml = simplexml_load_file($file[0]);
		if (!$xml) {
			return null;
		}

		return $xml;
	}

	/**
	 * Makes sure that the app that is being installed is a valid one. If so, updates the information on the
	 * database for the app, otherwise it will display an error.
	 *
	 * @param $name    string name of the app
	 * @param $version string version of the app
	 *
	 * @return bool false if unknown app or db error, true on success
	 * @throws Exception
	 *
	 * @since 5.0
	 */
	public function saveApp($name, $version) {
		$app          = Factory::getApplication();
		$table        = $this->getTable('DirectoryApps', 'JTable');
		$directoryApp = $table->getDirectoryAppByName($name);

		if (empty($directoryApp)) {
			//throw(new Exception('LNG_UNKNOWN_DIRECTORY_APP'));
			$directoryApp = new stdClass;
            $directoryApp->id = -1;
		}

		$table->id      = $directoryApp->id;
		$table->version = (string) $version;
		if (!$table->store()) {
			throw(new Exception($table->getError()));
		}

		return true;
	}

	/**
	 * Prepares a list of statuses for each directory app. Status 1 indicates that the current app
	 * has been installed, 0 for uninstalled ones.
	 *
	 * @since 5.0
	 */
	public function getAppStatuses() {
		$table = $this->getTable('DirectoryApps', 'JTable');
		$apps  = $table->getDirectoryApps(TYPE_DIRECTORY_APP);

		$statuses = array();
		foreach ($apps as $app) {
			$status = DIRECTORY_APP_UNINSTALLED;

			if (in_array($app->app_name, $this->directoryAppNames)) {
				$key = array_search($app->app_name, $this->directoryAppNames);
				if (file_exists($this->directoryAppPaths[$key])) {
					$status = DIRECTORY_APP_INSTALLED;
				}
			}

			if ($status == DIRECTORY_APP_INSTALLED) {
				if (!empty($app->version) && !empty($app->required_version) && version_compare($app->version, $app->required_version) == -1) {
					$status = DIRECTORY_APP_UPDATE;
				}
			}

			$statuses[$app->id] = $status;
		}

		return $statuses;
	}

	/**
	 * Prepares a list of statuses for each directory extension. Status 1 indicates that the current extension
	 * has been installed, 0 for uninstalled ones.
	 *
	 * @since 5.1.2
	 */
	public function getExtensionStatuses() {
		$table = $this->getTable('DirectoryApps', 'JTable');
		$apps  = $table->getDirectoryApps(TYPE_DIRECTORY_EXTENSION);

		$statuses = array();
		foreach ($apps as $app) {
			$status = DIRECTORY_APP_UNINSTALLED;

			if (in_array($app->app_name, $this->directoryExtensionsNames)) {
				$key = array_search($app->app_name, $this->directoryExtensionsNames);
				if (file_exists($this->directoryExtensionsPaths[$key])) {
					$status = DIRECTORY_APP_INSTALLED;
				}
			}

			if ($status == DIRECTORY_APP_INSTALLED) {
				if (!empty($app->version) && !empty($app->required_version) && version_compare($app->version, $app->required_version) == -1) {
					$status = DIRECTORY_APP_UPDATE;
				}
			}

			$statuses[$app->id] = $status;
		}

		return $statuses;
	}

	/**
	 * Check if elements for an array are contained at the beginning of a string
	 *
	 * @param unknown $haystack
	 * @param unknown $key
	 *
	 * @return boolean
	 */
	public function isFound($haystack, $key) {
		$found = false;
		foreach ($haystack as $elem) {
			if (strpos($key, $elem) === 0) {
				$found = true;
				break;
			}
		}
		return $found;
	}

	public function getPendingListings(){
		$table = $this->getTable('Company', 'JTable');
		$items = $table->getListingsByStatus(COMPANY_STATUS_CREATED, TOTAL_PENDING_ITEMS_DISPLAYED+1);
		return $items;
	}

	public function getPendingClaimApproval(){
		$table = $this->getTable('Company', 'JTable');
		$items = $table->getListingsByStatus(COMPANY_STATUS_CLAIMED, TOTAL_PENDING_ITEMS_DISPLAYED+1);
		return $items;
	}

	public function getPendingOffers(){
		$table = $this->getTable('Offer', 'JTable');
		$items = $table->getOffersByStatus(OFFER_STATUS_NEEDS_APPROVAL, TOTAL_PENDING_ITEMS_DISPLAYED+1);
		return $items;
	}

	public function getPendingEvents(){
		$table = $this->getTable('Event', 'JTable');
		$items = $table->getEventsByStatus(EVENT_STATUS_NEEDS_APPROVAL, TOTAL_PENDING_ITEMS_DISPLAYED+1);
		return $items;
	}

	public function getPendingReviews(){
		$table = $this->getTable('Review', 'JTable');
		$items = $table->getReviewsByStatus(REVIEW_STATUS_CREATED, TOTAL_PENDING_ITEMS_DISPLAYED+1);
		return $items;
	}

	public function getDatabaseDifferences() {
		$db = $this->getDbo();
		JModelLegacy::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/models', 'Database');
		$model = JModelLegacy::getInstance('Database', 'JBusinessDirectoryModel', array('ignore_request' => true));

		$installationSQL = $model->getInstallationDBSchema();
		$installationSQL = $db->replacePrefix($installationSQL);		
		$result = $model->dbDelta($installationSQL, false);
		
		return $result;
	}

	public function getSchemaVersion() {
		JModelLegacy::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/models', 'Database');
		$model = JModelLegacy::getInstance('Database', 'JBusinessDirectoryModel', array('ignore_request' => true));
		$schema = $model->getSchemaVersion();		
		
		return $schema;
	}
}