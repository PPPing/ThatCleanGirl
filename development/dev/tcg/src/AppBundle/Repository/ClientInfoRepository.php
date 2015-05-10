<?php

namespace AppBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
/**
 * ClientInfoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ClientInfoRepository extends DocumentRepository
{

    public function updateClientBasicInfo($clientInfo)
    {
        if(empty($clientInfo['id'])){
            throw new InvalidArgumentException("clientInfo : Id");
        }else{
            $id = $clientInfo['id'];
        }
        if(empty($clientInfo['clientName'])){
            $clientName="";
        }else{
            $clientName = $clientInfo['clientName'];
        }
        if(empty($clientInfo['driverLicense'])){
            $driverLicense="";
        }else{
            $driverLicense = $clientInfo['driverLicense'];
        }
        if(empty($clientInfo['tel'])){
            $tel="";
        }else{
            $tel = $clientInfo['tel'];
        }
        if(empty($clientInfo['birthday'])){
            $birthday=new DateTime("1980-01-01");
        }else{
            $birthday = date_create_from_format('Y-m-d\TH:i:sT', $clientInfo['birthday']);
        }
        if(empty($clientInfo['address'])){
            $address="";
        }else{
            $address = $clientInfo['address'];
        }

        if(empty($clientInfo['isActive'])){

            $isActive = false;

        }else{
            $isActive = $clientInfo['isActive'];

            if($isActive===true || $isActive =='true') {
                $isActive = true;
            }
            else{
                $isActive = false;
            }
           //
        }

        if(empty($clientInfo['startDate'])){
            $startDate = new DateTime("NOW");
        }else{
            $startDate = date_create_from_format('Y-m-d\TH:i:sT', $clientInfo['startDate']);
            //$startDate = $clientInfo['startDate'];
        }
        if(empty($clientInfo['price'])){
            $price=998;
        }else{
            $price = $clientInfo['price'];
        }
        if(empty($clientInfo['rotations'])){
            $r1=new stdClass();
            $r1->key = "week 1";
            $r1->value = "";
            $r2=new stdClass();
            $r2->key = "week 2";
            $r2->value = "";
            $r3=new stdClass();
            $r3->key = "week 3";
            $r3->value = "";
            $r4=new stdClass();
            $r4->key = "week 4";
            $r4->value = "";

            $rotations = array($r1,$r2,$r3,$r4);
        }else{
            $rotations = $clientInfo['rotations'];
        }
        if(empty($clientInfo['remark'])){
            $remark ="";
        }else{
            $remark = $clientInfo['remark'];
        }

        $clientInfo = $this->createQueryBuilder()
            ->findAndUpdate()
            ->returnNew()
            ->field('id')->equals($id)
            // Update found job
            ->field('clientName')->set($clientName)
            ->field('driverLicense')->set($driverLicense)
            ->field('clientName')->set($clientName)
            ->field('tel')->set($tel)
            ->field('birthday')->set($birthday)
            ->field('address')->set($address)
            ->field('isActive')->set($isActive)
            ->field('startDate')->set($startDate)
            ->field('price')->set($price)
            ->field('rotations')->set($rotations)
            ->field('remark')->set($remark)
            ->field('modifyTime')->set(new \MongoDate())
            ->getQuery()
            ->execute();
        return $clientInfo;

    }

    public function updateClientJobDetail($id,$jobDetail){
        //create a log channel
        $log = new Logger('BaseDocument');
        $log->pushHandler(new StreamHandler('C:/xampp/htdocs/github/ThatCleanGirl/development/dev/tcg/app/logs/your.log', Logger::DEBUG));

        if($jobDetail["key"]["has"] === true || $jobDetail["key"]["has"] === "true"){
            $jobDetail["key"]["has"] = true;
        }else{
            $jobDetail["key"]["has"] = false;
        }
        if($jobDetail["pet"]["has"] === true||$jobDetail["pet"]["has"] === "true"){
            $jobDetail["pet"]["has"] = true;
        }else{
            $jobDetail["pet"]["has"] = false;
        }
        $log->addDebug(json_encode($jobDetail,JSON_PRETTY_PRINT));
        $clientInfo = $this->createQueryBuilder()
            ->findAndUpdate()
            ->returnNew()
            ->field('id')->equals($id)
            // Update found job
            ->field('jobDetail')->set($jobDetail)
            ->field('modifyTime')->set(new \MongoDate())
            ->getQuery()
            ->execute();
        return $clientInfo;
    }

    public function updateClientPaymentInfo($clientInfo){
        if(empty($clientInfo['id'])){
            throw new InvalidArgumentException("clientInfo : Id");
        }else{
            $id = $clientInfo['id'];
        }

        if(empty($clientInfo['paymentType'])){
            $paymentType="cash";
        }else{
            $paymentType = $clientInfo['paymentType'];
        }
        if(empty($clientInfo['invoiceNeeded'])){
            $invoiceNeeded = true;
        }else{

            $invoiceNeeded = $clientInfo['invoiceNeeded'];

            if($invoiceNeeded===true || $invoiceNeeded =='true') {
                $invoiceNeeded = true;
            }
            else{
                $invoiceNeeded = false;
            }
        }
        if(empty($clientInfo['invoiceTitle'])){
            $invoiceTitle="";
        }else{
            $invoiceTitle = $clientInfo['invoiceTitle'];
        }

        $clientInfo = $this->createQueryBuilder()
            ->findAndUpdate()
            ->returnNew()
            ->field('id')->equals($id)
            // Update found job
            ->field('paymentType')->set($paymentType)
            ->field('invoiceNeeded')->set($invoiceNeeded)
            ->field('invoiceTitle')->set($invoiceTitle)
            ->field('modifyTime')->set(new \MongoDate())
            ->getQuery()
            ->execute();
        return $clientInfo;
    }

    public function generateClientId()
    {
        $exist = true;

        $clientId = $this->randomClientId();
        while($exist) {
            $clientId = $this->randomClientId();
            $exist =  !($this->isUniqueClientId($clientId));
        }
        return $clientId;

    }

    public function findAllActiveClient(){
        $activeClients = $this->findBy(array("isActive"=>true));

        return $activeClients;
    }

    private function isUniqueClientId($clientId)
    {
        $result = $this->findOneBy(array("clientId"=>$clientId));

        if($result==null){
            return true;
        }

        return false;
    }

    private function randomClientId()
    {
        $randomNum = mt_rand ( 10000000 , 99999999 );
        $clientId = floor($randomNum/10000).'-'.$randomNum%10000;

        return $clientId;
    }


}