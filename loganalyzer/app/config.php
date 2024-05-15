<?php
if ( !defined('IN_PHPLOGCON') )
{
	die('Hacking attempt');
	exit;
}
$CFG['UserDBEnabled'] = false;
$CFG['UserDBServer'] = 'localhost';
$CFG['UserDBPort'] = 3306;
$CFG['UserDBName'] = 'loganalyzer'; 
$CFG['UserDBPref'] = 'logcon_'; 
$CFG['UserDBUser'] = 'root';
$CFG['UserDBPass'] = '';
$CFG['UserDBLoginRequired'] = true;
$CFG['UserDBAuthMode'] = 'USERDB_AUTH_LDAP';
$CFG['LDAPServer'] = 'example.com';
$CFG['LDAPPort'] = 3268;
$CFG['LDAPBaseDN'] = 'DC=example,DC=com';
$CFG['LDAPSearchFilter'] = '(&(&(objectClass=User)(objectCategory=Person)(sAMAccountName=user*)(memberOf=CN=Loganalyzer,OU=MGMT,OU=LOGGER,OU=Cloud_DataPro,OU=Servers,DC=example,DC=com))(!(userAccountControl=514)))';
$CFG['LDAPUidAttribute'] = 'sAMAccountName';
$CFG['LDAPBindDN'] = 'CN=logger,OU=MGMT,OU=LOGGER,OU=Cloud,OU=Servers,DC=example,DC=com';
$CFG['LDAPBindPassword'] = 'password';
$CFG['MiscShowDebugMsg'] = 0;				// if enabled, you will get additional output on certain places
$CFG['MiscDebugToSyslog'] = 0;				// if enabled, debug messages from LogAnalyzer will be send to syslog on linux, and into the EventLog on Windows
$CFG['MiscShowDebugGridCounter'] = 0;		// Only for debugging purposes, will add a counter column into the grid!
$CFG["MiscShowPageRenderStats"] = 1;		// If enabled, you will see Pagerender Settings
$CFG['MiscEnableGzipCompression'] = 1;		// If enabled, LogAnalyzer will use gzip compression for output, we recommend
											// to have this option enabled, it will highly reduce bandwith usage. 
$CFG['MiscMaxExecutionTime'] = 30;			// LogAnalyzer will try to overwrite the default script timeout with this value during runtime!
											// This can of course only work if LogAnalyzer is allowed to changed the script timeout. 
$CFG['DebugUserLogin'] = 1;					// if enabled, you will see additional informations on failed logins
$CFG['PrependTitle'] = "";					// If set, this	text will be prepended withint the title tag
$CFG['ViewUseTodayYesterday'] = 0;			// If enabled, the date from today and yesterday is displayed as "today" and "yesterday"
$CFG['ViewMessageCharacterLimit'] = 0;		// Default character limit for the message gets trunscated! 0 means NO trunscation.
$CFG['ViewStringCharacterLimit'] = 0;		// Default character limit for all other string type fields before they get trunscated! 0 means NO trunscation.
$CFG['ViewEntriesPerPage'] = 10;			// Default number of syslog entries shown per page
$CFG['ViewEnableDetailPopups'] = 1;			// If enabled, you will see additional Details for each syslog message on mouse over. 
$CFG['ViewDefaultTheme'] = "default";		// This sets the default theme the user is going to see when he opens LogAnalyzer the first time. 
$CFG['ViewDefaultLanguage'] = "en";			// Sets the default display language
$CFG['ViewEnableAutoReloadSeconds'] = 360;	// If "ViewEnableAutoReloadSeconds" is set to anything higher the 0 (which means disabled), this means auto reload is enabled by default. 
$CFG['SearchCustomButtonCaption'] = "I'd like to feel sad";	// Default caption for the custom fast search button
$CFG['SearchCustomButtonSearch'] = "error";					// Default search string for the custom search button
// $CFG['SearchCustomButtonCaption'] = "Хочу расстроиться!";
// $CFG['SearchCustomButtonSearch'] = "ошибка";
$CFG['EnableContextLinks'] = 1;				// if enabled, context links within the messages will automatically be created and added. Set this to 0 to disable all context links. 
$CFG['EnableIPAddressResolve'] = 1;			// If enabled, IP Addresses inline messages are automatically resolved and the result is added in brackets {} behind the IP Address
$CFG['SuppressDuplicatedMessages'] = 0;		// If enabled, duplicated messages will be suppressed in the main display. 
$CFG['TreatNotFoundFiltersAsTrue'] = 0;		// If you filter / search for messages, and the fields you are filtering for is not found, the filter result is treaten as TRUE! 
$CFG['PopupMenuTimeout'] = 3000;			// This variable defines the default timeout value for popup menus in milliseconds. (those menus which popup when you click on the value of a field.
$CFG['PhplogconLogoUrl'] = "";				// Put an Url to a custom toplogo you want to use.
$CFG['InlineOnlineSearchIcons'] = 1;		// Show online search icons
$CFG['UseProxyServerForRemoteQueries'] = "";// If empty no proxy server will be used. If set to a proxy server url like 127.0.0.1:8080, LogAnalyzer will use this server for url queries like the updatecheck. 
$CFG['HeaderDefaultEncoding'] = "ENC_UTF8";	// Set default character encoding
$CFG['DefaultFont'] = "Arial";				// Sets the default Font to be used in Loganalyzer
$CFG['DefaultFontSize'] = "100";			// Sets the percentage font size, default is 100 (Percent). Can be changed up to 200 and down to 50 with good results.  
$CFG['InjectHtmlHeader'] = "";				// Use this variable to inject custom html into the html <head> area!
$CFG['InjectBodyHeader'] = "";				// Use this variable to inject custom html into the begin of the <body> area!
$CFG['InjectBodyFooter'] = "";				// Use this variable to inject custom html into the end of the <body> area!
$CFG['Columns'] = array ( SYSLOG_DATE, SYSLOG_FACILITY, SYSLOG_SEVERITY, SYSLOG_HOST, SYSLOG_SYSLOGTAG, SYSLOG_PROCESSID, SYSLOG_MESSAGETYPE, SYSLOG_MESSAGE );
$CFG['DefaultViewsID'] = "";
$CFG['Search'][] = array ( "DisplayName" => "Syslog Warnings and Errors", "SearchQuery" => "filter=severity%3A0%2C1%2C2%2C3%2C4&search=Search" );
$CFG['Search'][] = array ( "DisplayName" => "Syslog Errors", "SearchQuery" => "filter=severity%3A0%2C1%2C2%2C3&search=Search" );
$CFG['Search'][] = array ( "DisplayName" => "All messages from the last hour", "SearchQuery" => "filter=datelastx%3A1&search=Search" );
$CFG['Search'][] = array ( "DisplayName" => "All messages from last 12 hours", "SearchQuery" => "filter=datelastx%3A2&search=Search" );
$CFG['Search'][] = array ( "DisplayName" => "All messages from last 24 hours", "SearchQuery" => "filter=datelastx%3A3&search=Search" );
$CFG['Search'][] = array ( "DisplayName" => "All messages from last 7 days", "SearchQuery" => "filter=datelastx%3A4&search=Search" );
$CFG['Search'][] = array ( "DisplayName" => "All messages from last 31 days", "SearchQuery" => "filter=datelastx%3A5&search=Search" );
// $CFG['Search'][] = array ( "DisplayName" => "Предупреждения и ошибки", "SearchQuery" => "filter=severity%3A0%2C1%2C2%2C3%2C4&search=Search" );
// $CFG['Search'][] = array ( "DisplayName" => "Только ошибки", "SearchQuery" => "filter=severity%3A0%2C1%2C2%2C3&search=Search" );
// $CFG['Search'][] = array ( "DisplayName" => "Сообщения за последний час", "SearchQuery" => "filter=datelastx%3A1&search=Search" );
// $CFG['Search'][] = array ( "DisplayName" => "Сообщения за последние 12 часов", "SearchQuery" => "filter=datelastx%3A2&search=Search" );
// $CFG['Search'][] = array ( "DisplayName" => "Сообщения за последние 24 часа", "SearchQuery" => "filter=datelastx%3A3&search=Search" );
// $CFG['Search'][] = array ( "DisplayName" => "Сообщения за последние 7 дней", "SearchQuery" => "filter=datelastx%3A4&search=Search" );
// $CFG['Search'][] = array ( "DisplayName" => "Сообщения за последний 31 день", "SearchQuery" => "filter=datelastx%3A5&search=Search" );
$CFG['Charts']['Chart1'] = array ( "DisplayName" => "Top Hosts", "chart_type" => CHART_BARS_HORIZONTAL, "chart_width" => 400, "chart_field" => SYSLOG_HOST, "maxrecords" => 10, "showpercent" => 0, "chart_enabled" => 1 );
$CFG['Charts']['Chart2'] = array ( "DisplayName" => "SyslogTags", "chart_type" => CHART_CAKE, "chart_width" => 400, "chart_field" => SYSLOG_SYSLOGTAG, "maxrecords" => 10, "showpercent" => 0, "chart_enabled" => 1 );
$CFG['Charts']['Chart3'] = array ( "DisplayName" => "Severity Occurences", "chart_type" => CHART_BARS_VERTICAL, "chart_width" => 400, "chart_field" => SYSLOG_SEVERITY, "maxrecords" => 10, "showpercent" => 1, "chart_enabled" => 1 );
$CFG['Charts']['Chart4'] = array ( "DisplayName" => "Usage by Day", "chart_type" => CHART_CAKE, "chart_width" => 400, "chart_field" => SYSLOG_DATE, "maxrecords" => 10, "showpercent" => 1, "chart_enabled" => 1 );
$CFG['DiskAllowed'][] = "/var/log/"; 
$CFG['DefaultSourceID'] = 'Source1';
$CFG['Sources']['Source1']['ID'] = 'Source1';
$CFG['Sources']['Source1']['Name'] = 'RSyslog';
$CFG['Sources']['Source1']['ViewID'] = 'SYSLOG';
$CFG['Sources']['Source1']['SourceType'] = SOURCE_PDO;
$CFG['Sources']['Source1']['DBTableType'] = 'monitorware';
$CFG['Sources']['Source1']['DBType'] = DB_PGSQL;
$CFG['Sources']['Source1']['DBServer'] = 'postgresql';
$CFG['Sources']['Source1']['DBName'] = 'rsyslog';
$CFG['Sources']['Source1']['DBUser'] = 'rsyslog';
$CFG['Sources']['Source1']['DBPassword'] = 'password';
$CFG['Sources']['Source1']['DBTableName'] = 'systemevents';
$CFG['Sources']['Source1']['DBEnableRowCounting'] = false;

?>
