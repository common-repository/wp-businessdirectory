<?php
/**
 * @package    JBD.Libraries
 *
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

JLoader::registerAlias('JApplicationAdministrator',         '\\MVC\\Application\\AdministratorApplication', '5.0');
JLoader::registerAlias('JApplicationHelper',                '\\MVC\\Application\\ApplicationHelper', '5.0');
JLoader::registerAlias('JApplicationBase',                  '\\MVC\\Application\\BaseApplication', '5.0');
JLoader::registerAlias('JApplicationCli',                   '\\MVC\\Application\\CliApplication', '5.0');
JLoader::registerAlias('JApplicationCms',                   '\\MVC\\Application\\CMSApplication', '5.0');
JLoader::registerAlias('JApplicationDaemon',                '\\MVC\\Application\\DaemonApplication', '5.0');
JLoader::registerAlias('JApplicationSite',                  '\\MVC\\Application\\SiteApplication', '5.0');
JLoader::registerAlias('JApplicationWeb',                   '\\MVC\\Application\\WebApplication', '5.0');
JLoader::registerAlias('JApplicationWebClient',             '\\MVC\\Application\\Web\\WebClient', '5.0');

JLoader::registerAlias('JModelAdmin',                       '\\MVC\model\\AdminModel', '5.0');
JLoader::registerAlias('JModelForm',                        '\\MVC\model\\FormModel', '5.0');
JLoader::registerAlias('JModelItem',                        '\\MVC\model\\ItemModel', '5.0');
JLoader::registerAlias('JModelList',                        '\\MVC\model\\ListModel', '5.0');
JLoader::registerAlias('JModelLegacy',                      '\\MVC\model\\BaseDatabaseModel', '5.0');
JLoader::registerAlias('JViewCategories',                   '\\MVC\view\\CategoriesView', '5.0');
JLoader::registerAlias('JViewCategory',                     '\\MVC\view\\CategoryView', '5.0');
JLoader::registerAlias('JViewCategoryfeed',                 '\\MVC\view\\CategoryFeedView', '5.0');
JLoader::registerAlias('JViewLegacy',                       '\\MVC\view\\WPHtmlView', '5.0'); // was HtmlView but replaced because the plugin was loaded before wordpress headers
JLoader::registerAlias('WPViewLegacy',                      '\\MVC\view\\WPHtmlView', '5.0');
JLoader::registerAlias('JControllerAdmin',                  '\\MVC\controller\\AdminController', '5.0');
JLoader::registerAlias('JControllerLegacy',                 '\\MVC\controller\\BaseController', '5.0');
JLoader::registerAlias('JControllerForm',                   '\\MVC\controller\\FormController', '5.0');
JLoader::registerAlias('JTableInterface',                   '\\MVC\table\\TableInterface', '5.0');
JLoader::registerAlias('JTable',                            '\\MVC\table\\Table', '5.0');
JLoader::registerAlias('JTableNested',                      '\\MVC\\table\\Nested', '5.0');

JLoader::registerAlias('JLanguageAssociations',             '\\MVC\language\\Associations', '5.0');
JLoader::registerAlias('JLanguage',                         '\\MVC\language\\Language', '5.0');
JLoader::registerAlias('JLanguageHelper',                   '\\MVC\language\\LanguageHelper', '5.0');
JLoader::registerAlias('JLanguageStemmer',                  '\\MVC\language\\LanguageStemmer', '5.0');
JLoader::registerAlias('JLanguageMultilang',                '\\MVC\language\\Multilanguage', '5.0');
JLoader::registerAlias('JText',                             '\\MVC\language\\Text', '5.0');
JLoader::registerAlias('JLanguageTransliterate',            '\\MVC\language\\Transliterate', '5.0');
JLoader::registerAlias('JLanguageStemmerPorteren',          '\\MVC\language\\Stemmer\\Porteren', '5.0');
JLoader::registerAlias('JLanguageWrapperText',              '\\MVC\language\\Wrapper\\JTextWrapper', '4.0');
JLoader::registerAlias('JLanguageWrapperHelper',            '\\MVC\language\\Wrapper\\LanguageHelperWrapper', '4.0');
JLoader::registerAlias('JLanguageWrapperTransliterate',     '\\MVC\language\\Wrapper\\TransliterateWrapper', '4.0');

JLoader::registerAlias('JPagination',                       '\\MVC\\Pagination\\Pagination', '5.0');
JLoader::registerAlias('JPaginationObject',                 '\\MVC\\Pagination\\PaginationObject', '5.0');  

JLoader::registerAlias('JPath',    							'\\MVC\filesystem\\path', '4.0');
JLoader::registerAlias('JFile',    							'\\MVC\filesystem\\file', '4.0');
JLoader::registerAlias('JFolder',    						'\\MVC\filesystem\\folder', '4.0');
JLoader::registerAlias('JFilesystemWrapperPath',            '\\MVC\filesystem\wrapper\\FilesystemWrapperPath', '4.0');
JLoader::registerAlias('JFilesystemWrapperFolder',            '\\MVC\filesystem\wrapper\\FilesystemWrapperFolder', '4.0');
JLoader::registerAlias('JFilesystemWrapperFile',            '\\MVC\filesystem\wrapper\\FilesystemWrapperFile', '4.0');

JLoader::registerAlias('JClientHelper',                     '\\MVC\\CMS\\Client\\ClientHelper', '5.0');
JLoader::registerAlias('JClientWrapperHelper',              '\\MVC\\CMS\\Client\\ClientWrapper', '5.0');
JLoader::registerAlias('JClientFtp',                        '\\MVC\\CMS\\Client\\FtpClient', '5.0');

JLoader::registerAlias('JLog',                              '\\MVC\\CMS\\Log\\Log', '5.0');
JLoader::registerAlias('JLogEntry',                              '\\MVC\\CMS\\Log\\LogEntry', '5.0');

JLoader::registerAlias('JBufferStreamHandler',              '\\MVC\\CMS\\Utility\\BufferStreamHandler', '5.0');

JLoader::registerAlias('JDate',                             '\\MVC\\CMS\\Date\\Date', '5.0');

JLoader::registerAlias('JZip',                              '\\MVC\\CMS\\Archive\\Zpi', '5.0');

JLoader::registerAlias('JFilterInput',                      '\\MVC\\CMS\\InputFilter', '5.0');
JLoader::registerAlias('JFilterOutput',                     '\\MVC\\CMS\\OutputFilter', '5.0');
JLoader::registerAlias('JFilterWrapperOutput',              '\\MVC\\CMS\\Wrapper\\OutputFilterWrapper', '4.0');

JLoader::registerAlias('ArrayHelper',                       '\\MVC\\utilities\\ArrayHelper', '5.0');

JLoader::registerAlias('JRoute',                            '\\MVC\\router\\Route', '5.0');

JLoader::registerAlias('JLayoutBase',                       '\\MVC\\layout\\BaseLayout', '5.0');
JLoader::registerAlias('JLayoutFile',                       '\\MVC\\layout\\FileLayout', '5.0');
JLoader::registerAlias('JLayoutHelper',                     '\\MVC\\layout\\LayoutHelper', '5.0');
JLoader::registerAlias('JLayout',                           '\\MVC\\layout\\LayoutInterface', '5.0');

JLoader::registerAlias('JForm',                             '\\MVC\\form\\Form', '5.0');
JLoader::registerAlias('JFormField',                        '\\MVC\\form\\FormField', '5.0');
JLoader::registerAlias('JFormHelper',                       '\\MVC\\form\\FormHelper', '5.0');
JLoader::registerAlias('JFormRule',                         '\\MVC\\form\\FormRule', '5.0');
JLoader::registerAlias('JFormWrapper',                      '\\MVC\\form\\FormWrapper', '4.0');
JLoader::registerAlias('JFormFieldAuthor',                  '\\MVC\\form\\Field\\AuthorField', '5.0');
JLoader::registerAlias('JFormFieldCaptcha',                 '\\MVC\\form\\Field\\CaptchaField', '5.0');
JLoader::registerAlias('JFormFieldChromeStyle',             '\\MVC\\form\\Field\\ChromestyleField', '5.0');
JLoader::registerAlias('JFormFieldContenthistory',          '\\MVC\\form\\Field\\ContenthistoryField', '5.0');
JLoader::registerAlias('JFormFieldContentlanguage',         '\\MVC\\form\\Field\\ContentlanguageField', '5.0');
JLoader::registerAlias('JFormFieldContenttype',             '\\MVC\\form\\Field\\ContenttypeField', '5.0');
JLoader::registerAlias('JFormFieldEditor',                  '\\MVC\\form\\Field\\EditorField', '5.0');
JLoader::registerAlias('JFormFieldFrontend_Language',       '\\MVC\\form\\Field\\FrontendlanguageField', '5.0');
JLoader::registerAlias('JFormFieldHeadertag',               '\\MVC\\form\\Field\\HeadertagField', '5.0');
JLoader::registerAlias('JFormFieldHelpsite',                '\\MVC\\form\\Field\\HelpsiteField', '5.0');
JLoader::registerAlias('JFormFieldLastvisitDateRange',      '\\MVC\\form\\Field\\LastvisitdaterangeField', '5.0');
JLoader::registerAlias('JFormFieldLimitbox',                '\\MVC\\form\\Field\\LimitboxField', '5.0');
JLoader::registerAlias('JFormFieldMedia',                   '\\MVC\\form\\Field\\MediaField', '5.0');
JLoader::registerAlias('JFormFieldMenu',                    '\\MVC\\form\\Field\\MenuField', '5.0');
JLoader::registerAlias('JFormFieldMenuitem',                '\\MVC\\form\\Field\\MenuitemField', '5.0');
JLoader::registerAlias('JFormFieldModuleOrder',             '\\MVC\\form\\Field\\ModuleorderField', '5.0');
JLoader::registerAlias('JFormFieldModulePosition',          '\\MVC\\form\\Field\\ModulepositionField', '5.0');
JLoader::registerAlias('JFormFieldModuletag',               '\\MVC\\form\\Field\\ModuletagField', '5.0');
JLoader::registerAlias('JFormFieldOrdering',                '\\MVC\\form\\Field\\OrderingField', '5.0');
JLoader::registerAlias('JFormFieldPlugin_Status',           '\\MVC\\form\\Field\\PluginstatusField', '5.0');
JLoader::registerAlias('JFormFieldRedirect_Status',         '\\MVC\\form\\Field\\RedirectStatusField', '5.0');
JLoader::registerAlias('JFormFieldRegistrationDateRange',   '\\MVC\\form\\Field\\RegistrationdaterangeField', '5.0');
JLoader::registerAlias('JFormFieldStatus',                  '\\MVC\\form\\Field\\StatusField', '5.0');
JLoader::registerAlias('JFormFieldTag',                     '\\MVC\\form\\Field\\TagField', '5.0');
JLoader::registerAlias('JFormFieldTemplatestyle',           '\\MVC\\form\\Field\\TemplatestyleField', '5.0');
JLoader::registerAlias('JFormFieldUserActive',              '\\MVC\\form\\Field\\UseractiveField', '5.0');
JLoader::registerAlias('JFormFieldUserGroupList',           '\\MVC\\form\\Field\\UsergrouplistField', '5.0');
JLoader::registerAlias('JFormFieldUserState',               '\\MVC\\form\\Field\\UserstateField', '5.0');
JLoader::registerAlias('JFormFieldUser',                    '\\MVC\\form\\Field\\UserField', '5.0');
JLoader::registerAlias('JFormRuleBoolean',                  '\\MVC\\form\\rule\\BooleanRule', '5.0');
JLoader::registerAlias('JFormRuleCalendar',                 '\\MVC\\form\\rule\\CalendarRule', '5.0');
JLoader::registerAlias('JFormRuleCaptcha',                  '\\MVC\\form\\rule\\CaptchaRule', '5.0');
JLoader::registerAlias('JFormRuleColor',                    '\\MVC\\form\\rule\\ColorRule', '5.0');
JLoader::registerAlias('JFormRuleEmail',                    '\\MVC\\form\\rule\\EmailRule', '5.0');
JLoader::registerAlias('JFormRuleEquals',                   '\\MVC\\form\\rule\\EqualsRule', '5.0');
JLoader::registerAlias('JFormRuleNotequals',                '\\MVC\\form\\rule\\NotequalsRule', '5.0');
JLoader::registerAlias('JFormRuleNumber',                   '\\MVC\\form\\rule\\NumberRule', '5.0');
JLoader::registerAlias('JFormRuleOptions',                  '\\MVC\\form\\rule\\OptionsRule', '5.0');
JLoader::registerAlias('JFormRulePassword',                 '\\MVC\\form\\rule\\PasswordRule', '5.0');
JLoader::registerAlias('JFormRuleRules',                    '\\MVC\\form\\rule\\RulesRule', '5.0');
JLoader::registerAlias('JFormRuleTel',                      '\\MVC\\form\\rule\\TelRule', '5.0');
JLoader::registerAlias('JFormRuleUrl',                      '\\MVC\\form\\rule\\UrlRule', '5.0');
JLoader::registerAlias('JFormRuleUsername',                 '\\MVC\\form\\rule\\UsernameRule', '5.0');

JLoader::registerAlias('JUser',                             '\\MVC\\User\\User', '5.0');
JLoader::registerAlias('JUserHelper',                       '\\MVC\\User\\UserHelper', '5.0');
JLoader::registerAlias('JUserWrapperHelper',                '\\MVC\\User\\UserWrapper', '4.0');

JLoader::registerAlias('JToolbar',                          '\\MVC\toolbar\\Toolbar', '5.0');
JLoader::registerAlias('JToolbarButton',                    '\\MVC\toolbar\\ToolbarButton', '5.0');
JLoader::registerAlias('JToolbarButtonConfirm',             '\\MVC\toolbar\\Button\\ConfirmButton', '5.0');
JLoader::registerAlias('JToolbarButtonCustom',              '\\MVC\toolbar\\Button\\CustomButton', '5.0');
JLoader::registerAlias('JToolbarButtonHelp',                '\\MVC\toolbar\\Button\\HelpButton', '5.0');
JLoader::registerAlias('JToolbarButtonLink',                '\\MVC\toolbar\\Button\\LinkButton', '5.0');
JLoader::registerAlias('JToolbarButtonPopup',               '\\MVC\toolbar\\Button\\PopupButton', '5.0');
JLoader::registerAlias('JToolbarButtonSeparator',           '\\MVC\toolbar\\Button\\SeparatorButton', '5.0');
JLoader::registerAlias('JToolbarButtonSlider',              '\\MVC\toolbar\\Button\\SliderButton', '5.0');
JLoader::registerAlias('JToolbarButtonStandard',            '\\MVC\toolbar\\Button\\StandardButton', '5.0');
JLoader::registerAlias('JToolbarHelper',                    '\\MVC\toolbar\\ToolbarHelper', '5.0');
JLoader::registerAlias('JButton',                           '\\MVC\toolbar\\ToolbarButton', '5.0');

JLoader::registerAlias('JDatabaseQueryMysqli',    			'\\MVC\database\\JDatabaseQueryMysqli', '4.0');
JLoader::registerAlias('WPDatabaseDriver',    				'\\MVC\database\\WPDatabaseDriver', '4.0');

JLoader::registerAlias('Input',    							'\\MVC\\Input\\Input', '4.0');

JLoader::registerAlias('JFactory',                          '\\MVC\Factory', '5.0');
JLoader::registerAlias('JObject',                           '\\MVC\object\\CMSObject', '5.0');

JLoader::registerAlias('JHtml',                             '\\MVC\\html\\HTMLHelper', '5.0');

JLoader::registerAlias('JEditor',                           '\\MVC\\Editor\\Editor', '5.0');

JLoader::registerAlias('JDocument',                         '\\MVC\\Document\\Document', '5.0');
JLoader::registerAlias('JDocumentError',                    '\\MVC\\Document\\ErrorDocument', '5.0');
JLoader::registerAlias('JDocumentFeed',                     '\\MVC\\Document\\FeedDocument', '5.0');
JLoader::registerAlias('JDocumentHtml',                     '\\MVC\\Document\\HtmlDocument', '5.0');
JLoader::registerAlias('JDocumentImage',                    '\\MVC\\Document\\ImageDocument', '5.0');
JLoader::registerAlias('JDocumentJson',                     '\\MVC\\Document\\JsonDocument', '5.0');
JLoader::registerAlias('JDocumentOpensearch',               '\\MVC\\Document\\OpensearchDocument', '5.0');
JLoader::registerAlias('JDocumentRaw',                      '\\MVC\\Document\\RawDocument', '5.0');
JLoader::registerAlias('JDocumentRenderer',                 '\\MVC\\Document\\DocumentRenderer', '5.0');
JLoader::registerAlias('JDocumentXml',                      '\\MVC\\Document\\XmlDocument', '5.0');
JLoader::registerAlias('JDocumentRendererFeedAtom',         '\\MVC\\Document\\Renderer\\Feed\\AtomRenderer', '5.0');
JLoader::registerAlias('JDocumentRendererFeedRss',          '\\MVC\\Document\\Renderer\\Feed\\RssRenderer', '5.0');
JLoader::registerAlias('JDocumentRendererHtmlComponent',    '\\MVC\\Document\\Renderer\\Html\\ComponentRenderer', '5.0');
JLoader::registerAlias('JDocumentRendererHtmlHead',         '\\MVC\\Document\\Renderer\\Html\\HeadRenderer', '5.0');
JLoader::registerAlias('JDocumentRendererHtmlMessage',      '\\MVC\\Document\\Renderer\\Html\\MessageRenderer', '5.0');
JLoader::registerAlias('JDocumentRendererHtmlModule',       '\\MVC\\Document\\Renderer\\Html\\ModuleRenderer', '5.0');
JLoader::registerAlias('JDocumentRendererHtmlModules',      '\\MVC\\Document\\Renderer\\Html\\ModulesRenderer', '5.0');
JLoader::registerAlias('JDocumentRendererAtom',             '\\MVC\\Document\\Renderer\\Feed\\AtomRenderer', '4.0');
JLoader::registerAlias('JDocumentRendererRSS',              '\\MVC\\Document\\Renderer\\Feed\\RssRenderer', '4.0');
JLoader::registerAlias('JDocumentRendererComponent',        '\\MVC\\Document\\Renderer\\Html\\ComponentRenderer', '4.0');
JLoader::registerAlias('JDocumentRendererHead',             '\\MVC\\Document\\Renderer\\Html\\HeadRenderer', '4.0');
JLoader::registerAlias('JDocumentRendererMessage',          '\\MVC\\Document\\Renderer\\Html\\MessageRenderer', '4.0');
JLoader::registerAlias('JDocumentRendererModule',           '\\MVC\\Document\\Renderer\\Html\\ModuleRenderer', '4.0');
JLoader::registerAlias('JDocumentRendererModules',          '\\MVC\\Document\\Renderer\\Html\\ModulesRenderer', '4.0');
JLoader::registerAlias('JFeedEnclosure',                    '\\MVC\\Document\\Feed\\FeedEnclosure', '5.0');
JLoader::registerAlias('JFeedImage',                        '\\MVC\\Document\\Feed\\FeedImage', '5.0');
JLoader::registerAlias('JFeedItem',                         '\\MVC\\Document\\Feed\\FeedItem', '5.0');
JLoader::registerAlias('JOpenSearchImage',                  '\\MVC\\Document\\Opensearch\\OpensearchImage', '5.0');
JLoader::registerAlias('JOpenSearchUrl',                    '\\MVC\\Document\\Opensearch\\OpensearchUrl', '5.0');

JLoader::registerAlias('JSession',                          '\\MVC\\Session\\Session', '5.0');
JLoader::registerAlias('JSessionExceptionUnsupported',      '\\MVC\\Session\\Exception\\UnsupportedStorageException', '5.0');
JLoader::registerAlias('JCrypt',                            '\\MVC\\Crypt\\Crypt', '5.0');

JLoader::registerAlias('JModuleHelper',                     '\\MVC\\Helper\\ModuleHelper', '5.0');