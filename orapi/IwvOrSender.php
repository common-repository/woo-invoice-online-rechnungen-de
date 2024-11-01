<?php

class IwvOrSender {

    public $bank;
    public $kontonr;
    public $iban;
    public $bic;
    public $blz;
    public $stnr;
    public $ustidnr;
    #
    public $company;
    public $firstName;
    public $lastName;
    public $street;
    public $streetNumber;
    public $postal;
    public $city;
    public $country;
    public $telephone;
    public $fax;

    public function loadFromDatabase() {
        /* Company Information */
        $senderDb = (array) get_option('iwvor_sender');
        $this->company = isset($senderDb['company']) ? $senderDb['company'] : '';
        $this->firstName = isset($senderDb['firstName']) ? $senderDb['firstName'] : '';
        $this->lastName = isset($senderDb['lastname']) ? $senderDb['lastname'] : '';
        $this->street = isset($senderDb['street']) ? $senderDb['street'] : '';
        $this->streetNumber = isset($senderDb['streetNumber']) ? $senderDb['streetNumber'] : '';
        $this->postal = isset($senderDb['postal']) ? $senderDb['postal'] : '';
        $this->city = isset($senderDb['city']) ? $senderDb['city'] : '';
        $this->country = isset($senderDb['country']) ? $senderDb['country'] : '';
        $this->fax = '';
        /* Bank Information */
        $bankDb = (array) get_option('iwvor_bank');
        $this->bank = isset($bankDb['bank']) ? $bankDb['bank'] : '';
        $this->kontonr = isset($bankDb['accountNumber']) ? $bankDb['accountNumber'] : '';
        $this->iban = isset($bankDb['iban']) ? $bankDb['iban'] : '';
        $this->bic = isset($bankDb['bic']) ? $bankDb['bic'] : '';
    }

}
