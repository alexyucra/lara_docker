<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class paypalController extends Controller
{
    
    public function index() {
        
        // Parametros da configuração do Gateway
        $params = $this->paypalplus_config();
        $params['companyname']='sacpay';
        $params['systemurl']='';
        $params['langpaynow']='';
        $params['name']='';
        $params['paymentmethod']='';
        # return $params;


        return $this->system_config($params);
    }

    public function paypalplus_config() {
        return array(
            // Nome de exibição amigável para o gateway
            'FriendlyName' => array(
                'Type' => 'System',
                'Value' => 'Gofas PayPal Plus',
            ),	
            // Client ID Live
            'clientid' => array(
                'FriendlyName' => 'Live Client ID',
                'Type' => 'text',
                'Size' => '60',
                'Default' => '',
                'Description' => 'Insira o Client ID de acesso à REST API do <a style="text-decoration:underline;" target="_blank" href="https://developer.paypal.com/developer/applications/">seu aplicativo</a>.',
            ),
            // Client Secret Live
            'clientsecret' => array(
                'FriendlyName' => 'Live Client Secret',
                'Type' => 'text',
                'Size' => '60',
                'Default' => '',
                'Description' => 'Insira o Client Secret do seu aplicativo.',
            ),
            // Client ID Sandbox
            'clientidsandbox' => array(
                'FriendlyName' => 'Sandbox Client ID',
                'Type' => 'text',
                'Size' => '60',
                'Default' => '',
                'Description' => 'Insira o Client ID Sandbox (ambiente de testes) do seu aplicativo.',
            ),
            // Client Secret Sandbox
            'clientsecretsandbox' => array(
                'FriendlyName' => 'Sandbox Client Secret',
                'Type' => 'text',
                'Size' => '60',
                'Default' => '',
                'Description' => 'Insira o Client Secret Sandbox (ambiente de testes) do seu aplicativo.',
            ),
            // Testar?
            'sandboxmode' => array(
                'FriendlyName' => 'Sandbox',
                'Type' => 'yesno',
                'Description' => 'Marque essa opção se você estiver utilizando o par de chaves "Client_Id" e "Client_Secret" do modo Sandbox (Desenvolvimento).',
            ),
            // Debug?
            'debugmode' => array(
                'FriendlyName' => 'Debug',
                'Type' => 'yesno',
                'Description' => 'Marque essa opção para exibir resultados e erros retornados pela API PayPal e API interna do WHMCS.<b><br/>Por segurança, NÃO use isso em produção, apenas para testes ou se precisar diagnosticar erros.',
            ),
            // log?
            'logcallbackmode' => array(
                'FriendlyName' => 'Log callback',
                'Type' => 'yesno',
                'Description' => 'Salva no <a style="text-decoration: underline;" href="/admin/systemactivitylog.php" target="_blank">log do sistema</a> o resultado do processamento dos dados recebidos pelo PayPal, para fins de diagnóstico e aprendizado',
            ),
            
            // whmcs admin
            'admin' => array(
                'FriendlyName' => 'Administrador atribuído',
                'Type' => 'text',
                'Size' => '10',
                'Default' => '',
                'Description' => 'Insira o nome de usuário ou ID do administrador que será atribuído as transações. Necessário para usar a API interna do WHMCS.',
            ),
            // customfield CPF
            'customfieldcpf' => array(
                'FriendlyName' => 'Ordem do campo CPF ou CNPJ',
                'Type' => 'text',
                'Size' => '10',
                'Default' => '0',
                'Description' => 'Insira a ordem de exibição do <a style="text-decoration: underline;" href="/admin/configcustomfields.php" target="_blank">campo personalizado</a> criado para coletar o CPF do cliente.',
            ),
            // customfield CNPJ
            'customfieldcnpj' => array(
                'FriendlyName' => 'Ordem do campo CNPJ',
                'Type' => 'text',
                'Size' => '10',
                'Default' => '1',
                'Description' => 'Insira a ordem de exibição do <a style="text-decoration: underline;" href="/admin/configcustomfields.php" target="_blank">campo personalizado</a> criado para coletar o CNPJ do cliente. Deixe em branco se você usa apenas um campo para CPF e CNPJ.',
            ),
    
            // Botão "Finalizar Pagamento"
            'paybuttonimage' => array(
                'FriendlyName' => 'Imagem do botão "Finalizar Pagamento"',
                'Type' => 'text',
                'Size' => '90',
                'Default' => '',
                'Description' => '<br/>Insira o URL da imagem que será usada como botão "Finalizar Pagamento" (tamanho recomendado: entre 160x40px e 339x40px).',
            ),
            // Crédito
            'credits' => array(
                'Description' => '<div style="background: #dde9f9;padding: 5px 15px;">
                <p>Versão 0.1.7</p>
                &copy; '.date('Y').' <a target="_blank" href="https://gofas.net">Gofas.net</a> | <a target="_blank" href="https://gofas.net/?p=8294">Documentação</a> | <a target="_blank" href="https://gofas.net/?p=7858">Suporte</a><br/>
                </div>',
            ),
        );
    }

    public function system_config($params){
        // Parametros do sistema
        dd($params);
        die();
        $companyName		= $params['companyname'];
        $systemUrl			= $params['systemurl'];
        //$returnUrl			= $systemUrl.'/modules/gateways/gofaspaypalplus/callback.php';
        $langPayNow			= $params['langpaynow'];
        $moduleDisplayName	= $params['name'];
        $moduleName			= $params['paymentmethod'];

        // Web Experience Profile / perfil de experiência
        $profile_name 		= 'Gofas PayPal Plus';
        $experience_profile = '{
            "name":"'.$profile_name.'",
            "presentation":{
                "brand_name":"'.$companyName.'",
                "logo_image":"'.$systemUrl.'/assets/img/logo.png",
                "locale_code":"BR"
                },
            "input_fields":{
                "allow_note":false,
                "no_shipping":1,
                "address_override":1
                },
            "flow_config":{
                "landing_page_type":"billing",
                "bank_txn_pending_url":"'.$systemUrl.'"
                }
            }';
            
        // Parametros da configuração do Gateway
        $moduleVersion		= '0.1.5'; // Releases: https://github.com/gofas/whmcs-paypalplus/releases
        $sandbox			= $params['sandboxmode'];
        if ($params['customfieldcpf']) {
            $customfCPF			= $params['customfieldcpf'];
        } elseif (!$params['customfieldcpf']) {
            $customfCPF			= "0";
        }
        if ($params['customfieldcnpj']) {
            $customfCNPJ		= $params['customfieldcnpj'];
        } elseif (!$params['customfieldcnpj']) {
            $customfCNPJ		= "1";
        }

        if ($sandbox) {
            $client_id			= $params['clientidsandbox'];
            $client_secret		= $params['clientsecretsandbox'];
            $pp_host			= 'https://api.sandbox.paypal.com';
            $pp_mode			= 'sandbox';
        }elseif(!$sandbox) {
            $client_id			= $params['clientid'];
            $client_secret		= $params['clientsecret'];
            $pp_host			= 'https://api.paypal.com';
            $pp_mode			= 'live';
        }

        if (stripos($_SERVER['REQUEST_URI'], 'viewinvoice.php')){
            $isInvoive 			= true;
        } else {
            $isInvoive 			= false;
        }

        if ($isInvoive){
            $debug				= $params['debugmode'];
        } else {
            $debug				= false;
        }

        $buttonLocation = 'outside';

        if ($params['paybuttonimage']){
            $payButtonCss		= '
            .payment-btn-container button.continueButton {
            background: url('.json_encode($params['paybuttonimage']).') no-repeat center;
            border: none;
            padding: 20px;
            width: 100%;
            display: none;
        }
        .payment-btn-container button.continueButton:hover {
            text-decoration: none;
            cursor: pointer;
        }
        ';
            $payButton			= '<button type="submit" class="continueButton" id="continueButton" onclick="ppp.doContinue(); return true;">  </button>';
            
        }elseif(!$params['paybuttonimage']){
            $payButtonCss		= '
            .payment-btn-container button.continueButton {
            background: #009cde;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 22px;
            width: 100%;
            display: none;
        }
        .payment-btn-container button.continueButton:hover {
            background: #017aad;
            text-decoration: none;
            cursor: pointer;
        }
        ';
            $payButton			= '<button type="submit" class="continueButton" id="continueButton" onclick="ppp.doContinue(); return true;">Finalizar Pagamento</button>';
            
        }
        // CSS da fatura
        $css="";
        $css				.= '
        <style type="text/css">
        '.$payButtonCss.'
        a, a:hover {cursor: pointer;}
        #lightbox {
            z-index: 999999;
            width: 100%;
            height:100%;
            position: absolute;
            top: 0;
            left:0;
            background: rgba(255, 255, 255, 0.9);
            display: none;
        }
        #lightboxspan {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 280px;
            height: 68px;
            margin-top: -25%;
            margin-left: -140px;
            font-weight: bold;
            background: url('.$systemUrl.'/assets/img/loading.gif) no-repeat center;
        }
        @media (min-width: 768px) { .col-sm-7 { width: 48.333333%; } .col-sm-5 { width: 50%; } }
        </style>';

        if($params['admin']) {
            $whmcsAdmin			= $params['admin'];
        }elseif(!$params['admin']){
            $whmcsAdmin 		= 1;
        }
        // Parametros da Fatura
        $invoiceID				= $params['invoiceid'];
        $invoiceDescription 	= $params["description"];
        $invoiceAmount			= $params['amount'];

        // Parametros do Cliente
        $userID 			= $params['clientdetails']['id'];
        $firstname 			= $params['clientdetails']['firstname'];
        $lastname 			= $params['clientdetails']['lastname'];
        $email				= $params['clientdetails']['email'];
        $CCompanyName		= $params['clientdetails']['companyname'];
        $address1 			= $params['clientdetails']['address1'];
        $address2 			= $params['clientdetails']['address2'];
        $city 				= $params['clientdetails']['city'];
        $state				= $params['clientdetails']['state'];
        $postcode			= preg_replace("/[^\da-z]/i", "",$params['clientdetails']['postcode']);
        $country			= $params['clientdetails']['country'];
        $phone				= preg_replace('/[^\da-z]/i', '', $params['clientdetails']['phonenumber']);

        /************************  CPF & CNPJ ************************/
        $cpfStr = preg_replace("/[^\da-z]/i", "", $params["clientdetails"]["customfields"]["$customfCPF"]["value"]); // Primeiro campo personalizado
        $cnpjStr = preg_replace("/[^\da-z]/i", "", $params["clientdetails"]["customfields"]["$customfCNPJ"]["value"]); // Segundo campo personalizado

        if (strlen($cpfStr) === 10) { // Adiciona um dígido 0 (zero) ao início do CPF se esse possui apenas 10 caracteres
            $cpf = '0'.$cpfStr;
            
            if (strlen($cnpjStr) === 13) {
                $cnpj = '0'.$cnpjStr; // Adiciona um dígido 0 (zero) ao início do CNPJ se esse possui apenas 13 caracteres
                $payerTaxId_1 = $cpf;
                $payerTaxIdType_1 = 'BR_CPF';
                $payerTaxId_2 = $cnpj;
                $payerTaxIdType_2 = 'BR_CNPJ';
                
            } elseif (strlen($cnpjStr) === 14) {
                $cnpj = $cnpjStr;
                $payerTaxId_1 = $cpf;
                $payerTaxIdType_1 = 'BR_CPF';
                $payerTaxId_2 = $cnpj;
                $payerTaxIdType_2 = 'BR_CNPJ';
                
            } elseif (strlen($cnpjStr) !== 14 || strlen($cnpjStr) !== 13) {
                $cnpj = false;
                $payerTaxId_1 = $cpf;
                $payerTaxIdType_1 = 'BR_CPF';
                $payerTaxId_2 = false;
                $payerTaxIdType_2 = false;
            }
        }
        elseif (strlen($cpfStr) === 11) { // Adiciona um dígido 0 (zero) ao início do CPF e interpreta CPF como CNPJ se esse possui 13 caracteres
            $cpf = $cpfStr;
            
            if (strlen($cnpjStr) === 13) {
                $cnpj = '0'.$cnpjStr; // Adiciona um dígido 0 (zero) ao início do CNPJ se esse possui apenas 13 caracteres
                $payerTaxId_1 = $cpf;
                $payerTaxIdType_1 = 'BR_CPF';
                $payerTaxId_2 = $cnpj;
                $payerTaxIdType_2 = 'BR_CNPJ';
                
            } elseif (strlen($cnpjStr) === 14) {
                $cnpj = $cnpjStr;
                $payerTaxId_1 = $cpf;
                $payerTaxIdType_1 = 'BR_CPF';
                $payerTaxId_2 = $cnpj;
                $payerTaxIdType_2 = 'BR_CNPJ';
                
            } elseif (strlen($cnpjStr) !== 14 || strlen($cnpjStr) !== 13) {
                $cnpj = $cpf;
                $payerTaxId_1 = $cpf;
                $payerTaxIdType_1 = 'BR_CPF';
                $payerTaxId_2 = false;
                $payerTaxIdType_2 = false;
            }
        }
        elseif (strlen($cpfStr) === 13) { // Adiciona um dígido 0 (zero) ao início do CPF e interpreta CPF como CNPJ se esse possui 13 caracteres
            $cpf = false; 
            $cnpj = '0'.$cpfStr;
            $payerTaxId_1 = false;
            $payerTaxIdType_1 = false;
            $payerTaxId_2 = $cnpj;
            $payerTaxIdType_2 = 'BR_CNPJ';
            
        }
        elseif (strlen($cpfStr) === 14) { // Interpreta CPF como CNPJ se esse possui 14 caracteres
            $cpf 				= false;
            $cnpj				= $cpfStr;
            $payerTaxId_1 		= false;
            $payerTaxIdType_1	= false;
            $payerTaxId_2		= $cnpj;
            $payerTaxIdType_2	= 'BR_CNPJ';
            
        }
        else {
            $cpf 				= $cpfStr;
            if (strlen($cnpjStr) === 13) {
                $cnpj = '0'.$cnpjStr; // Adiciona um dígido 0 (zero) ao início do CNPJ se esse possui apenas 13 caracteres
                $payerTaxId_1 = $cpf;
                $payerTaxIdType_1 = 'BR_CPF';
                $payerTaxId_2 = $cnpj;
                $payerTaxIdType_2 = 'BR_CNPJ';
                
            } elseif (strlen($cnpjStr) === 14) {
                $cnpj = $cnpjStr;
                $payerTaxId_1 = $cpf;
                $payerTaxIdType_1 = 'BR_CPF';
                $payerTaxId_2 = $cnpj;
                $payerTaxIdType_2 = 'BR_CNPJ';
                
            } elseif (strlen($cnpjStr) !== 14 || strlen($cnpjStr) !== 13) {
                $cnpj = false;
                $payerTaxId_1 = $cpf;
                $payerTaxIdType_1 = 'BR_CPF';
                $payerTaxId_2 = false;
                $payerTaxIdType_2 = false;
            }
        }

    }
}
