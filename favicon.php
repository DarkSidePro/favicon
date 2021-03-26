<?php
/**
* 2007-2019 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

include('class/ImageResize.php');

if (!defined('_PS_VERSION_')) {
    exit;
}

class Favicon extends Module
{
    protected $config_form = false;


    public function __construct()
    {
        $this->name = 'favicon';
        $this->tab = 'seo';
        $this->version = '1.0.0';
        $this->author = 'Dark-Side.pro';
        $this->need_instance = 1;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Favicon');
        $this->description = $this->l('This module add a lots of favicons type');

        $this->confirmUninstall = $this->l('');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    private function createTab()
    {
        $response = true;
        $parentTabID = Tab::getIdFromClassName('AdminDarkSideMenu');
        if ($parentTabID) {
            $parentTab = new Tab($parentTabID);
        } else {
            $parentTab = new Tab();
            $parentTab->active = 1;
            $parentTab->name = array();
            $parentTab->class_name = "AdminDarkSideMenu";
            foreach (Language::getLanguages() as $lang) {
                $parentTab->name[$lang['id_lang']] = "Dark-Side.pro";
            }
            $parentTab->id_parent = 0;
            $parentTab->module = '';
            $response &= $parentTab->add();
        }
        $parentTab_2ID = Tab::getIdFromClassName('AdminDarkSideMenuSecond');
        if ($parentTab_2ID) {
            $parentTab_2 = new Tab($parentTab_2ID);
        } else {
            $parentTab_2 = new Tab();
            $parentTab_2->active = 1;
            $parentTab_2->name = array();
            $parentTab_2->class_name = "AdminDarkSideMenuSecond";
            foreach (Language::getLanguages() as $lang) {
                $parentTab_2->name[$lang['id_lang']] = "Dark-Side Config";
            }
            $parentTab_2->id_parent = $parentTab->id;
            $parentTab_2->module = '';
            $response &= $parentTab_2->add();
        }
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = "AdministratorFavicon";
        $tab->name = array();
        foreach (Language::getLanguages() as $lang) {
            $tab->name[$lang['id_lang']] = "Favicon";
        }
        $tab->id_parent = $parentTab_2->id;
        $tab->module = $this->name;
        $response &= $tab->add();
        return $response;
    }

    private function tabRem()
    {
        $id_tab = Tab::getIdFromClassName('AdministratorFavicon');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            $tab->delete();
        }
        $parentTab_2ID = Tab::getIdFromClassName('AdminDarkSideMenuSecond');
        if ($parentTab_2ID) {
            $tabCount_2 = Tab::getNbTabs($parentTab_2ID);
            if ($tabCount_2 == 0) {
                $parentTab_2 = new Tab($parentTab_2ID);
                $parentTab_2->delete();
            }
        }
        $parentTabID = Tab::getIdFromClassName('AdminDarkSideMenu');
        if ($parentTabID) {
            $tabCount = Tab::getNbTabs($parentTabID);
            if ($tabCount == 0) {
                $parentTab = new Tab($parentTabID);
                $parentTab->delete();
            }
        }
        return true;
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('FAVICON_COLOR', '#000');
        $this->createTab();

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader');
    }

    public function uninstall()
    {
        Configuration::deleteByName('FAVICON_COLOR');
        $this->tabRem();

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        return $this->postProcess().$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitFaviconModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('Enter a favicon color in Hex'),
                        'name' => 'FAVICON_COLOR',
                        'label' => $this->l('Color'),
                    ),
                    array(
                        'type' => 'file',
                        'label' => $this->l('Favicon image'),
                        'name' => 'FAVICON_IMG',
                        'desc' => $this->l('Favicon must be a squere. Minimum dimmension is 310x310px'),
                    ),
                    array(
                        'type' => 'file',
                        'label' => $this->l('Favicon logo'),
                        'name' => 'FAVICON_LOGO',
                        'desc' => $this->l('Minimum dimmension is 310x150px'),
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'FAVICON_COLOR' => Configuration::get('FAVICON_COLOR', null),
            'FAVICON_IMG' => Configuration::get('FAVICON_IMG', null),
            'FAVICON_LOGO' => Configuration::get('FAVICON_LOGO', null),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();
        $color = Tools::getValue('FAVICON_COLOR');
        $color2 = Configuration::get('FAVICON_COLOR');

        if ($color != $color2) {
            Configuration::updateValue('FAVICON_COLOR', $color);
            return $this->displayConfirmation($this->trans('Settings updated.', array(), 'Admin.Notifications.Success'));
        }


        if (isset($_FILES['FAVICON_IMG']) && !empty($_FILES['FAVICON_IMG'])) {
            $target_dir = $this->local_path.'views/img/favicons/';
            $target_file = $target_dir . basename($_FILES['FAVICON_IMG']['name']);
            if ($error = ImageManager::validateUpload($_FILES['FAVICON_IMG'], 4000000)) {
                Configuration::updateValue('FAVICON_ICO', false);
                return $this->displayError($error);
            } else {
                $our_image = $_FILES['FAVICON_IMG']['tmp_name'];
                $file = $_FILES['FAVICON_IMG']['name'];
                $ext =  substr($file, strrpos($file, '.')+1);
                $preoutput = $this->local_path.'views/img/favicons/preoutput.png';

                if (function_exists('exif_imagetype')) {
                    if (exif_imagetype($our_image) == IMAGETYPE_GIF) {
                        $pre = imagepng(imagecreatefromgif($our_image), $preoutput);
                    } elseif (exif_imagetype($our_image) == IMAGETYPE_BMP) {
                        $pre = imagepng(imagecreatefrombmp($our_image), $preoutput);
                    } elseif (exif_imagetype($our_image) == IMAGETYPE_JPEG) {
                        $pre = imagepng(imagecreatefromjpeg($_FILES['image']['tmp_name']), $preoutput);
                    } else {
                        $our_image = $preoutput;
                    }
                } else {
                    if ($ext == 'bmp') {
                        $pre = imagepng(imagecreatefrombmp($our_image), $preoutput);
                    } elseif ($ext == 'jpg' || $ext == 'jpeg') {
                        $pre = imagepng(imagecreatefromjpeg($our_image), $preoutput);
                    } elseif ($ext == 'gif') {
                        $pre = imagepng(imagecreatefromgif($our_image), $preoutput);
                    } else {
                        $pre = imagepng(imagecreatefrompng($our_image), $preoutput);
                    }
                }

                list($width, $height) = getimagesize($our_image);

                if ($width < 300 || $height < 300) {
                    $error = $this->displayError($this->trans('Image is too small. Use minimum 300x300px', array(), 'Admin.Favicon.Error'));
                    return $error;
                }

                if ($width != $height) {
                    $error = $this->displayError($this->trans('Image must be a squer.', array(), 'Admin.Favicon.Error'));
                    return $error;
                }

                $image = new \Gumlet\ImageResize($preoutput);
                $image->resizeToBestFit(57, 57);
                $image->save($this->local_path.'views/img/favicons/favicon57.png');
                $image->resizeToBestFit(114, 114);
                $image->save($this->local_path.'views/img/favicons/favicon114.png');
                $image->resizeToBestFit(72, 72);
                $image->save($this->local_path.'views/img/favicons/favicon72.png');
                $image->resizeToBestFit(144, 144);
                $image->save($this->local_path.'views/img/favicons/favicon144.png');
                $image->resizeToBestFit(60, 60);
                $image->save($this->local_path.'views/img/favicons/favicon60.png');
                $image->resizeToBestFit(120, 120);
                $image->save($this->local_path.'views/img/favicons/favicon120.png');
                $image->resizeToBestFit(76, 76);
                $image->save($this->local_path.'views/img/favicons/favicon76.png');
                $image->resizeToBestFit(152, 152);
                $image->save($this->local_path.'views/img/favicons/favicon152.png');
                $image->resizeToBestFit(196, 196);
                $image->save($this->local_path.'views/img/favicons/favicon196.png');
                $image->resizeToBestFit(96, 96);
                $image->save($this->local_path.'views/img/favicons/favicon96.png');
                $image->resizeToBestFit(32, 32);
                $image->save($this->local_path.'views/img/favicons/favicon32.png');
                $image->resizeToBestFit(16, 16);
                $image->save($this->local_path.'views/img/favicons/favicon16.png');
                $image->resizeToBestFit(128, 128);
                $image->save($this->local_path.'views/img/favicons/favicon128.png');
                $image->resizeToBestFit(70, 70);
                $image->save($this->local_path.'views/img/favicons/favicon70.png');
                $image->resizeToBestFit(150, 150);
                $image->save($this->local_path.'views/img/favicons/favicon150.png');
                $image->resizeToBestFit(310, 310);
                $image->save($this->local_path.'views/img/favicons/favicon310.png');
                
                $error = $this->displayError($this->trans('Something wrong.', array(), 'Admin.Favicon.Error'));
                if (!file_exists($this->local_path.'views/img/favicons/favicon310.png')) {
                    Configuration::updateValue('FAVICON_ICO', false);
                    return $error;
                } elseif (!file_exists($this->local_path.'views/img/favicons/favicon150.png')) {
                    Configuration::updateValue('FAVICON_ICO', false);
                    return $error;
                } elseif (!file_exists($this->local_path.'views/img/favicons/favicon70.png')) {
                    Configuration::updateValue('FAVICON_ICO', false);
                    return $error;
                } elseif (!file_exists($this->local_path.'views/img/favicons/favicon128.png')) {
                    Configuration::updateValue('FAVICON_ICO', false);
                    return $error;
                } elseif (!file_exists($this->local_path.'views/img/favicons/favicon16.png')) {
                    Configuration::updateValue('FAVICON_ICO', false);
                    return $error;
                } elseif (!file_exists($this->local_path.'views/img/favicons/favicon32.png')) {
                    Configuration::updateValue('FAVICON_ICO', false);
                    return $error;
                } elseif (!file_exists($this->local_path.'views/img/favicons/favicon96.png')) {
                    Configuration::updateValue('FAVICON_ICO', false);
                    return $error;
                } elseif (!file_exists($this->local_path.'views/img/favicons/favicon196.png')) {
                    Configuration::updateValue('FAVICON_ICO', false);
                    return $error;
                } elseif (!file_exists($this->local_path.'views/img/favicons/favicon152.png')) {
                    Configuration::updateValue('FAVICON_ICO', false);
                    return $error;
                } elseif (!file_exists($this->local_path.'views/img/favicons/favicon76.png')) {
                    Configuration::updateValue('FAVICON_ICO', false);
                    return $error;
                } elseif (!file_exists($this->local_path.'views/img/favicons/favicon120.png')) {
                    Configuration::updateValue('FAVICON_ICO', false);
                    return $error;
                } elseif (!file_exists($this->local_path.'views/img/favicons/favicon60.png')) {
                    Configuration::updateValue('FAVICON_ICO', false);
                    return $error;
                } elseif (!file_exists($this->local_path.'views/img/favicons/favicon144.png')) {
                    Configuration::updateValue('FAVICON_ICO', false);
                    return $error;
                } elseif (!file_exists($this->local_path.'views/img/favicons/favicon72.png')) {
                    Configuration::updateValue('FAVICON_ICO', false);
                    return $error;
                } elseif (!file_exists($this->local_path.'views/img/favicons/favicon114.png')) {
                    Configuration::updateValue('FAVICON_ICO', false);
                    return $error;
                } elseif (!file_exists($this->local_path.'views/img/favicons/favicon57.png')) {
                    Configuration::updateValue('FAVICON_ICO', false);
                    return $error;
                }

                Configuration::updateValue('FAVICON_ICO', true);
            }
            return $this->displayConfirmation($this->trans('Settings updated.', array(), 'Admin.Notifications.Success'));
        }
        
        if (isset($_FILES['FAVICON_LOGO']) && !empty($_FILES['FAVICON_LOGO'])) {
            $target_dir = $this->local_path.'views/img/favicons/';
            $target_file = $target_dir . basename($_FILES['FAVICON_LOGO']['name']);
            if ($error = ImageManager::validateUpload($_FILES['FAVICON_IMG'], 4000000)) {
                Configuration::updateValue('FAVICON_LOGO', false);
                return $this->displayError($error);
            } else {
                $our_image = $_FILES['FAVICON_LOGO']['tmp_name'];
                $file = $_FILES['FAVICON_LOGO']['name'];
                $ext =  substr($file, strrpos($file, '.')+1);
                $preoutput = $this->local_path.'views/img/favicons/prelogo.png';

                if (function_exists('exif_imagetype')) {
                    if (exif_imagetype($our_image) == IMAGETYPE_GIF) {
                        $pre = imagepng(imagecreatefromgif($our_image), $preoutput);
                    } elseif (exif_imagetype($our_image) == IMAGETYPE_BMP) {
                        $pre = imagepng(imagecreatefrombmp($our_image), $preoutput);
                    } elseif (exif_imagetype($our_image) == IMAGETYPE_JPEG) {
                        $pre = imagepng(imagecreatefromjpeg($_FILES['image']['tmp_name']), $preoutput);
                    } else {
                        $our_image = $preoutput;
                    }
                } else {
                    if ($ext == 'bmp') {
                        $pre = imagepng(imagecreatefrombmp($our_image), $preoutput);
                    } elseif ($ext == 'jpg' || $ext == 'jpeg') {
                        $pre = imagepng(imagecreatefromjpeg($our_image), $preoutput);
                    } elseif ($ext == 'gif') {
                        $pre = imagepng(imagecreatefromgif($our_image), $preoutput);
                    } else {
                        $pre = imagepng(imagecreatefrompng($our_image), $preoutput);
                    }
                }

                list($width, $height) = getimagesize($our_image);

                if ($width < 310 || $height < 150) {
                    $error = $this->displayError($this->trans('Image is too small. Use minimum 310x150px', array(), 'Admin.Favicon.Error'));
                    return $error;
                }

                $image = new \Gumlet\ImageResize($preoutput);
                $image->resizeToBestFit(310, 150);
                $image->save($this->local_path.'views/img/favicons/faviconlogo.png');
                
                $error = $this->displayError($this->trans('Something wrong.', array(), 'Admin.Favicon.Error'));
                if (!file_exists($this->local_path.'views/img/favicons/faviconlogo.png')) {
                    Configuration::updateValue('FAVICON_LOGO', false);
                    return $error;
                }
                Configuration::updateValue('FAVICON_LOGO', true);
            }
            return $this->displayConfirmation($this->trans('Settings updated.', array(), 'Admin.Favicon..Success'));
        }
        
        $this->_clearCache($this->templateFile);
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $favicon = Configuration::get('FAVICON_ICO');
        if ($favicon == true) {
            $favicon57 = 'modules/favicon/views/img/favicons/favicon57.png';
            $favicon114 = 'modules/favicon/views/img/favicons/favicon114.png';
            $favicon72 = 'modules/favicon/views/img/favicons/favicon72.png';
            $favicon144 = 'modules/favicon/views/img/favicons/favicon144.png';
            $favicon60 = 'modules/favicon/views/img/favicons/favicon60.png';
            $favicon120 = 'modules/favicon/views/img/favicons/favicon120.png';
            $favicon76 = 'modules/favicon/views/img/favicons/favicon76.png';
            $favicon152 = 'modules/favicon/views/img/favicons/favicon152.png';
            $favicon196 = 'modules/favicon/views/img/favicons/favicon196.png';
            $favicon96 = 'modules/favicon/views/img/favicons/favicon96.png';
            $favicon32 = 'modules/favicon/views/img/favicons/favicon32.png';
            $favicon16 = 'modules/favicon/views/img/favicons/favicon16.png';
            $favicon128 = 'modules/favicon/views/img/favicons/favicon128.png';
            $favicon70 = 'modules/favicon/views/img/favicons/favicon70.png';
            $favicon150 = 'modules/favicon/views/img/favicons/favicon150.png';
            $favicon310 = 'modules/favicon/views/img/favicons/favicon310.png';

            $this->context->smarty->assign('favicons', array(
                '57' => $favicon57,
                '114' => $favicon114,
                '72' => $favicon72,
                '144' => $favicon144,
                '60' => $favicon60,
                '120' => $favicon120,
                '76' => $favicon76,
                '152' => $favicon152,
                '196' => $favicon196,
                '96' => $favicon96,
                '32' => $favicon32,
                '16' => $favicon16,
                '70' => $favicon70,
                '150' => $favicon150,
                '310' => $favicon310,
                '128' => $favicon128
    
            ));
        }
        
        $color = Configuration::get('FAVICON_COLOR');
        $logo = Configuration::get('FAVICON_LOGO');

        if ($logo == true) {
            $favLogo = 'modules/favicon/views/img/favicons/faviconLogo.png';

            $this->context->smarty->assign('favlogo', $favLogo);
        }

        $this->context->smarty->assign('favicolor', $color);
        $output = $this->display(__FILE__, 'views/templates/hook/favicon.tpl');
        return $output;
    }
}
