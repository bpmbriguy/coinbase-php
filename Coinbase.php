<?php


/**
 * Creates a Coinbase Pay Button
 * 
 * $button = new Coinbase();
 * $button->setAPIKey('Put Key Here'); //required
 * $button->setName('The name of the item for which you are collecting bitcoin'); //required
 * $button->setPrice('Price as a decimal string'); //required
 * $button->setCurrency('Price currency as an ISO 4217 code such as USD or BTC'); //optional -defaults to USD
 * $button->setCustom('Custom Callback Value); //optional
 * $button->setType('One of buy_now, donation, or subscription'); //optional - defaults to buy_now
 * $button->setStyle('One of buy_now_large, buy_now_small, donation_large, donation_small, custom_large, custom_small, or none'); //optional - defaults to buy_now_large
 * $button->setDescription('Longer description of the item in case you want it added to the user's transaction notes.'); //optional
 * 
 * $button->display(); // Outputs the button
 */
class Coinbase {
    
    private $api_key;
    private $name;
    private $price_string;
    private $price_currency;
    private $custom;
    private $description;
    private $type;
    private $style;
    
    function __construct() {
        $this->price_currency = 'USD';
        $this->type = "buy_now";
        $this->style = "buy_now_large";
        
    }
    
    function setAPIKey($key) {
        if(empty($key)){
            throw new Exception('API Key cannot be empty');
        }
        else{
        $this->api_key = $key;
        }
    }
    
    function setName($name) {
        if(empty($name)){
            throw new Exception('Name cannot be empty');
        }
        else{
            $this->name = $name;
        }
    }
    
    function setPrice($price) {
        if (preg_match('/^[0-9]+(?:\.[0-9]{0,2})?$/', $price))
        {
          $this->price_string = $price;
        }
        else
        {
          throw new Exception('Invalid Price');
        }  
    }
    
    function setCurrency($currency) {
        if(!array_key_exists($currency, get_iso_4217_currency_codes())){
            throw new Exception('Invalid Currency Type: Please use a standard ISO 4217 Currency Type');
        }
        else {
            $this->price_currency = $currency;
        } 
    }
    
    function setCustom($custom) {
        $this->custom = $custom;
    }
    
    function setDescription($desc) {
        $this->description = $desc;
    }
    
    function setType($type) {
        $this->$type = $type;
    }
    
    function setStyle($style){
        $this->style = $style;
    }
    
    function display() {
        
        if(empty($this->api_key) || empty($this->name) || empty($this->price_string))
               throw new Exception("Missing one of required variables: APIKey, Name, Price");
        
        $api_key = $this->api_key;
        $name = $this->name;
        $price = $this->price_string;
        $currency = $this->price_currency;
        $custom = $this->custom;
        $description = $this->description;
        $type = $this->type;
        $style = $this->style;

        $args = array(
              'name' => $name,
              'price_string' => $price,
              'price_currency_iso' => $currency,
              'custom' => $custom,
              'description' => $description,
              'type' => $type,
              'style' => $style);

        $buttonargs = array('button' => $args);
        $buttonargs['api_key'] = $api_key;

        $content = json_encode($buttonargs);
        $url = "https://coinbase.com/api/v1/buttons";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER,
                array("Content-type: application/json"));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
        $json_response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ( $status != 200 && $status != 201 ) {
            die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
        }
        curl_close($curl);
        $response = json_decode($json_response, true);
        $buttonhtml = "<a class='coinbase-button' data-code='" . $response['button']['code'] . "' href='https://coinbase.com/checkouts/" . $response['button']['code'] . "'>" .$response['button']['text']. "</a>
<script src='https://coinbase.com/assets/button.js' type='text/javascript'></script>";

        print $buttonhtml;
    }
    
}



function get_iso_4217_currency_codes(){
$a = array();
$a['BTC'] = array('Bitcoin');
$a['AFA'] = array('Afghan Afghani', '971');
$a['AWG'] = array('Aruban Florin', '533');
$a['AUD'] = array('Australian Dollars', '036');
$a['ARS'] = array('Argentine Pes', '&nbsp;&nbsp;&nbsp; 03');
$a['AZN'] = array('Azerbaijanian Manat', '944');
$a['BSD'] = array('Bahamian Dollar', '044');
$a['BDT'] = array('Bangladeshi Taka', '050');
$a['BBD'] = array('Barbados Dollar', '052');
$a['BYR'] = array('Belarussian Rouble', '974');
$a['BOB'] = array('Bolivian Boliviano', '068');
$a['BRL'] = array('Brazilian Real', '986');
$a['GBP'] = array('British Pounds Sterling', '826');
$a['BGN'] = array('Bulgarian Lev', '975');
$a['KHR'] = array('Cambodia Riel', '116');
$a['CAD'] = array('Canadian Dollars', '124');
$a['KYD'] = array('Cayman Islands Dollar', '136');
$a['CLP'] = array('Chilean Peso', '152');
$a['CNY'] = array('Chinese Renminbi Yuan', '156');
$a['COP'] = array('Colombian Peso', '170');
$a['CRC'] = array('Costa Rican Colon', '188');
$a['HRK'] = array('Croatia Kuna', '191');
$a['CPY'] = array('Cypriot Pounds', '196');
$a['CZK'] = array('Czech Koruna', '203');
$a['DKK'] = array('Danish Krone', '208');
$a['DOP'] = array('Dominican Republic Peso', '214');
$a['XCD'] = array('East Caribbean Dollar', '951');
$a['EGP'] = array('Egyptian Pound', '818');
$a['ERN'] = array('Eritrean Nakfa', '232');
$a['EEK'] = array('Estonia Kroon', '233');
$a['EUR'] = array('Euro', '978');
$a['GEL'] = array('Georgian Lari', '981');
$a['GHC'] = array('Ghana Cedi', '288');
$a['GIP'] = array('Gibraltar Pound', '292');
$a['GTQ'] = array('Guatemala Quetzal', '320');
$a['HNL'] = array('Honduras Lempira', '340');
$a['HKD'] = array('Hong Kong Dollars', '344');
$a['HUF'] = array('Hungary Forint', '348');
$a['ISK'] = array('Icelandic Krona', '352');
$a['INR'] = array('Indian Rupee', '356');
$a['IDR'] = array('Indonesia Rupiah', '360');
$a['ILS'] = array('Israel Shekel', '376');
$a['JMD'] = array('Jamaican Dollar', '388');
$a['JPY'] = array('Japanese yen', '392');
$a['KZT'] = array('Kazakhstan Tenge', '368');
$a['KES'] = array('Kenyan Shilling', '404');
$a['KWD'] = array('Kuwaiti Dinar', '414');
$a['LVL'] = array('Latvia Lat', '428');
$a['LBP'] = array('Lebanese Pound', '422');
$a['LTL'] = array('Lithuania Litas', '440');
$a['MOP'] = array('Macau Pataca', '446');
$a['MKD'] = array('Macedonian Denar', '807');
$a['MGA'] = array('Malagascy Ariary', '969');
$a['MYR'] = array('Malaysian Ringgit', '458');
$a['MTL'] = array('Maltese Lira', '470');
$a['BAM'] = array('Marka', '977');
$a['MUR'] = array('Mauritius Rupee', '480');
$a['MXN'] = array('Mexican Pesos', '484');
$a['MZM'] = array('Mozambique Metical', '508');
$a['NPR'] = array('Nepalese Rupee', '524');
$a['ANG'] = array('Netherlands Antilles Guilder', '532');
$a['TWD'] = array('New Taiwanese Dollars', '901');
$a['NZD'] = array('New Zealand Dollars', '554');
$a['NIO'] = array('Nicaragua Cordoba', '558');
$a['NGN'] = array('Nigeria Naira', '566');
$a['KPW'] = array('North Korean Won', '408');
$a['NOK'] = array('Norwegian Krone', '578');
$a['OMR'] = array('Omani Riyal', '512');
$a['PKR'] = array('Pakistani Rupee', '586');
$a['PYG'] = array('Paraguay Guarani', '600');
$a['PEN'] = array('Peru New Sol', '604');
$a['PHP'] = array('Philippine Pesos', '608');
$a['QAR'] = array('Qatari Riyal', '634');
$a['RON'] = array('Romanian New Leu', '946');
$a['RUB'] = array('Russian Federation Ruble', '643');
$a['SAR'] = array('Saudi Riyal', '682');
$a['CSD'] = array('Serbian Dinar', '891');
$a['SCR'] = array('Seychelles Rupee', '690');
$a['SGD'] = array('Singapore Dollars', '702');
$a['SKK'] = array('Slovak Koruna', '703');
$a['SIT'] = array('Slovenia Tolar', '705');
$a['ZAR'] = array('South African Rand', '710');
$a['KRW'] = array('South Korean Won', '410');
$a['LKR'] = array('Sri Lankan Rupee', '144');
$a['SRD'] = array('Surinam Dollar', '968');
$a['SEK'] = array('Swedish Krona', '752');
$a['CHF'] = array('Swiss Francs', '756');
$a['TZS'] = array('Tanzanian Shilling', '834');
$a['THB'] = array('Thai Baht', '764');
$a['TTD'] = array('Trinidad and Tobago Dollar', '780');
$a['TRY'] = array('Turkish New Lira', '949');
$a['AED'] = array('UAE Dirham', '784');
$a['USD'] = array('US Dollars', '840');
$a['UGX'] = array('Ugandian Shilling', '800');
$a['UAH'] = array('Ukraine Hryvna', '980');
$a['UYU'] = array('Uruguayan Peso', '858');
$a['UZS'] = array('Uzbekistani Som', '860');
$a['VEB'] = array('Venezuela Bolivar', '862');
$a['VND'] = array('Vietnam Dong', '704');
$a['AMK'] = array('Zambian Kwacha', '894');
$a['ZWD'] = array('Zimbabwe Dollar', '716');
return $a;
}
