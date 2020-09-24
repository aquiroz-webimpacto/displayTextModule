<?php
/*
 * 2007-2015 PrestaShop
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
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA

 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class DisplayTextModule extends Module
{

    public function __construct()
    {

        $this->name = 'displaytextmodule';
        $this->author = 'Aquiroz';
        $this->version = '1.0';
        $this->controllers = array('default');
        $this->bootstrap = true;
        $this->need_instance = 1;

        $this->displayName = $this->l('A Module Text Positions');
        $this->description = $this->l('Created for Webimpacto Iniciation');
        $this->confirmUninstall = $this->l('Estas seguro de querer remover el Modulo');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

        parent::__construct();
    }

    public function install()
    {

        if (!parent::install()
            or !$this->registerHook('displayHome')
            or !$this->registerHook('displayFooterBefore')

        ) {
            return false;
        }

        return true;

    }

    public function uninstall()
    {

        if (!parent::uninstall()
            or !$this->unregisterHook('displayHome')
            or !$this->unregisterHook('displayFooterBefore')
        ) {
            return false;
        }

        return true;

    }

    public function getContent()
    {
        return $this->postProcess() . $this->getForm();

    }

    public function getForm()
    {

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->identifier = $this->identifier;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        $helper->languages = $this->context->controller->getLanguages();
        $helper->default_form_language = $this->context->controller->default_form_language;
        $helper->allow_employee_form_lang = $this->context->controller->allow_employee_form_lang;
        $helper->title = $this->displayName;
        $helper->submit_action = 'displaytextmodule';

        /**definiendo nombres en los inputs */
        $helper->fields_value['texto_a'] = Configuration::get('MODULO_DISPLAY_TEXTO_A');
        $helper->fields_value['texto_b'] = Configuration::get('MODULO_DISPLAY_TEXTO_B');

        /**formm array aplicado */

        $this->form[0] = array(
            'form' => array(

                'legend' => array(
                    'title' => $this->displayName,
                ),
                'input' => array(
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Informacion A'),
                        'desc' => $this->l('Texto Pagina de Superior'),
                        'hint' => $this->l('Texto pagina de Superior'),
                        'name' => 'texto_a',
                        'lang' => false,
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Informacion B'),
                        'desc' => $this->l('Texto Pagina de Inferior'),
                        'hint' => $this->l('Texto pagina de Inferior'),
                        'name' => 'texto_b',
                        'lang' => false,
                    ),
                ),

                'submit' => array(
                    'title' => $this->l('Save'), // This is the button that saves the whole fieldset.
                ),

            ),

        );

        return $helper->generateForm($this->form);

    }

    public function postProcess()
    {

        if (Tools::isSubmit('displaytextmodule')) {
            $texto_a = Tools::getValue('texto_a');
            $texto_b = Tools::getValue('texto_b');

            Configuration::updateValue('MODULO_DISPLAY_TEXTO_A', $texto_a);
            Configuration::updateValue('MODULO_DISPLAY_TEXTO_B', $texto_b);

            return $this->displayConfirmation($this->l('Updated Successfully'));
        }

    }

    public function hookDisplayHome($params)
    {
        $texto_a = Configuration::get('MODULO_DISPLAY_TEXTO_A');
        $this->context->smarty->assign(array(

            'displayhome' => $texto_a,
        ));

        return $this->context->smarty->fetch($this->local_path . 'views/templates/hook/displayhome.tpl');

    }

    public function hookDisplayFooterBefore($params)
    {
        $texto_b = Configuration::get('MODULO_DISPLAY_TEXTO_B');
        $this->context->smarty->assign(array(

            'displayfooter' => $texto_b,
        ));
        return $this->context->smarty->fetch($this->local_path . 'views/templates/hook/displayfooter.tpl');

    }

}
