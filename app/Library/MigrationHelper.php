<?php
/**
 * Created by Decipher Lab.
 * User: Prabakar
 * Date: 04-Apr-18
 * Time: 12:35 PM
 */

namespace app\Library;


use App\Models\AppCommission;
use App\Models\CallingCardAccess;
use App\Models\CreditLimit;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Service;
use App\Models\ServiceConfig;
use App\Models\TrackStatus;
use App\Models\Transaction;
use App\Models\UserAccess;
use App\User;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class MigrationHelper
{

    static function migrate_track_status()
    {
        $tb_track_status = array(
            array('id' => '1', 'TransID' => 'ATGDEMO0002', 'order_id' => '8', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-08-21 11:23:17', 'created_by' => '90'),
            array('id' => '3', 'TransID' => 'ATGDEMO0003', 'order_id' => '9', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-08-21 11:23:55', 'created_by' => '90'),
            array('id' => '5', 'TransID' => 'ATGDEMO0004', 'order_id' => '10', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-08-21 11:25:09', 'created_by' => '90'),
            array('id' => '6', 'TransID' => 'ATGDEMO008', 'order_id' => NULL, 'status' => 'Topup failed, unable to topup the receiver mobile!', 'error_code' => '0', 'created_at' => '2017-08-21 11:58:27', 'created_by' => '90'),
            array('id' => '8', 'TransID' => 'ATGDEMO009', 'order_id' => NULL, 'status' => NULL, 'error_code' => NULL, 'created_at' => NULL, 'created_by' => NULL),
            array('id' => '10', 'TransID' => 'ATGDEMO006', 'order_id' => '11', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-08-21 11:59:58', 'created_by' => '90'),
            array('id' => '11', 'TransID' => NULL, 'order_id' => '12', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-08-21 13:12:53', 'created_by' => '90'),
            array('id' => '12', 'TransID' => 'ATGDEMO0010', 'order_id' => NULL, 'status' => 'Tama Pay Order Not Found and/or Order May Be Processed!', 'error_code' => '0', 'created_at' => '2017-08-21 13:35:11', 'created_by' => '90'),
            array('id' => '14', 'TransID' => 'ATGDEMO0011', 'order_id' => NULL, 'status' => 'Tama Pay Order Not Found and/or Order May Be Processed!', 'error_code' => '0', 'created_at' => '2017-08-21 13:35:27', 'created_by' => '90'),
            array('id' => '16', 'TransID' => 'ATGDEMO0012', 'order_id' => NULL, 'status' => 'Tama Pay Order Not Found and/or Order May Be Processed!', 'error_code' => '0', 'created_at' => '2017-08-21 13:35:49', 'created_by' => '90'),
            array('id' => '18', 'TransID' => 'ATGDEMO0013', 'order_id' => '13', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-08-21 13:36:46', 'created_by' => '90'),
            array('id' => '19', 'TransID' => NULL, 'order_id' => NULL, 'status' => 'Mobile number is not a TamaApp User!', 'error_code' => '0', 'created_at' => '2017-08-21 14:02:51', 'created_by' => '90'),
            array('id' => '20', 'TransID' => NULL, 'order_id' => NULL, 'status' => NULL, 'error_code' => NULL, 'created_at' => NULL, 'created_by' => NULL),
            array('id' => '21', 'TransID' => NULL, 'order_id' => '14', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-08-21 14:05:49', 'created_by' => '90'),
            array('id' => '22', 'TransID' => NULL, 'order_id' => '15', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-08-21 14:24:54', 'created_by' => '90'),
            array('id' => '24', 'TransID' => 'ATGDEMO00016', 'order_id' => '40', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-08-28 14:02:00', 'created_by' => '90'),
            array('id' => '25', 'TransID' => 'ATGDEMO36', 'order_id' => NULL, 'status' => 'Mobile number is not a TamaApp User!', 'error_code' => '0', 'created_at' => '2017-11-01 13:40:15', 'created_by' => '90'),
            array('id' => '26', 'TransID' => 'ATGDEMO37', 'order_id' => NULL, 'status' => 'Mobile number is not a TamaApp User!', 'error_code' => '0', 'created_at' => '2017-11-01 13:40:28', 'created_by' => '90'),
            array('id' => '27', 'TransID' => 'XXXX000457', 'order_id' => '464', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-03 07:39:01', 'created_by' => '90'),
            array('id' => '28', 'TransID' => 'XXXX000458', 'order_id' => '465', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-03 07:56:04', 'created_by' => '90'),
            array('id' => '29', 'TransID' => 'ATGDEMO59', 'order_id' => '471', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-03 10:50:52', 'created_by' => '90'),
            array('id' => '30', 'TransID' => 'ATGDEMO60', 'order_id' => '472', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-03 11:33:10', 'created_by' => '90'),
            array('id' => '31', 'TransID' => 'ATGDEMO61', 'order_id' => '473', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-03 11:50:05', 'created_by' => '90'),
            array('id' => '32', 'TransID' => 'ATGDEMO62', 'order_id' => '474', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-03 13:23:47', 'created_by' => '90'),
            array('id' => '33', 'TransID' => 'ATGDEMO63', 'order_id' => '475', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-03 13:30:40', 'created_by' => '90'),
            array('id' => '34', 'TransID' => 'ATGDEMO64', 'order_id' => '476', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-03 14:00:45', 'created_by' => '90'),
            array('id' => '35', 'TransID' => 'ATGDEMO65', 'order_id' => '479', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-03 14:14:27', 'created_by' => '90'),
            array('id' => '36', 'TransID' => 'ATGDEMO66', 'order_id' => '480', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-03 14:26:36', 'created_by' => '90'),
            array('id' => '37', 'TransID' => 'ATGDEMO67', 'order_id' => '481', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-03 14:28:48', 'created_by' => '90'),
            array('id' => '38', 'TransID' => 'ATGDEMO68', 'order_id' => '482', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-03 14:29:03', 'created_by' => '90'),
            array('id' => '39', 'TransID' => 'ATGDEMO70', 'order_id' => '483', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-03 15:03:44', 'created_by' => '90'),
            array('id' => '40', 'TransID' => 'ATGDEMO38', 'order_id' => '484', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-03 15:23:17', 'created_by' => '90'),
            array('id' => '41', 'TransID' => 'ATGDEMO71', 'order_id' => NULL, 'status' => 'Tama Pay Order Not Found and/or Order May Be Processed!', 'error_code' => '0', 'created_at' => '2017-11-03 15:25:55', 'created_by' => '90'),
            array('id' => '42', 'TransID' => 'ATGDEMO', 'order_id' => '496', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-04 07:06:32', 'created_by' => '90'),
            array('id' => '70', 'TransID' => 'ATGDEMO72', 'order_id' => '497', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-04 10:48:23', 'created_by' => '90'),
            array('id' => '71', 'TransID' => 'ATGDEMO73', 'order_id' => '498', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-04 10:50:11', 'created_by' => '90'),
            array('id' => '76', 'TransID' => 'ATGDEMO74', 'order_id' => '499', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-04 10:56:21', 'created_by' => '90'),
            array('id' => '77', 'TransID' => 'ATGDEMO75', 'order_id' => NULL, 'status' => 'Error, You dont have any credit limits!', 'error_code' => '0', 'created_at' => '2017-11-04 10:57:48', 'created_by' => '90'),
            array('id' => '78', 'TransID' => 'ATGDEMO76', 'order_id' => NULL, 'status' => 'Error, You dont have any credit limits!', 'error_code' => '0', 'created_at' => '2017-11-04 10:58:13', 'created_by' => '90'),
            array('id' => '79', 'TransID' => 'ATGDEMO77', 'order_id' => NULL, 'status' => 'Error, You dont have any credit limits!', 'error_code' => '0', 'created_at' => '2017-11-04 10:59:01', 'created_by' => '90'),
            array('id' => '80', 'TransID' => 'ATGDEMO78', 'order_id' => NULL, 'status' => 'Error, You dont have any credit limits!', 'error_code' => '0', 'created_at' => '2017-11-04 10:59:28', 'created_by' => '90'),
            array('id' => '81', 'TransID' => 'ATGDEMO79', 'order_id' => NULL, 'status' => 'Error, You dont have any credit limits!', 'error_code' => '0', 'created_at' => '2017-11-04 11:01:25', 'created_by' => '90'),
            array('id' => '83', 'TransID' => 'ATGDEMO80', 'order_id' => NULL, 'status' => 'Error, You dont have any credit limits!', 'error_code' => '0', 'created_at' => '2017-11-04 11:06:24', 'created_by' => '90'),
            array('id' => '84', 'TransID' => 'ATGDEMO81', 'order_id' => NULL, 'status' => 'Error, You dont have any credit limits!', 'error_code' => '0', 'created_at' => '2017-11-04 11:07:51', 'created_by' => '90'),
            array('id' => '85', 'TransID' => 'ATGDEMO82', 'order_id' => NULL, 'status' => 'Error, You dont have any credit limits!', 'error_code' => '0', 'created_at' => '2017-11-04 11:10:24', 'created_by' => '90'),
            array('id' => '86', 'TransID' => 'ATGDEMO83', 'order_id' => NULL, 'status' => 'Error, You dont have any credit limits!', 'error_code' => '0', 'created_at' => '2017-11-04 11:14:33', 'created_by' => '90'),
            array('id' => '87', 'TransID' => 'ATGDEMO86', 'order_id' => NULL, 'status' => 'Error, You dont have any credit limits!', 'error_code' => '0', 'created_at' => '2017-11-04 11:17:39', 'created_by' => '90'),
            array('id' => '88', 'TransID' => 'ATGDEMO87', 'order_id' => NULL, 'status' => 'Error, You dont have any credit limits!', 'error_code' => '0', 'created_at' => '2017-11-04 11:19:04', 'created_by' => '90'),
            array('id' => '89', 'TransID' => 'ATGDEMO88', 'order_id' => NULL, 'status' => 'Error, You dont have any credit limits!', 'error_code' => '0', 'created_at' => '2017-11-04 11:19:36', 'created_by' => '90'),
            array('id' => '90', 'TransID' => 'ATGDEMO89', 'order_id' => NULL, 'status' => 'Error, You dont have any credit limits!', 'error_code' => '0', 'created_at' => '2017-11-04 11:21:45', 'created_by' => '90'),
            array('id' => '91', 'TransID' => 'ATGDEMO90', 'order_id' => NULL, 'status' => 'Error, You dont have any credit limits!', 'error_code' => '0', 'created_at' => '2017-11-06 08:16:15', 'created_by' => '90'),
            array('id' => '92', 'TransID' => 'ATGDEMO91', 'order_id' => NULL, 'status' => NULL, 'error_code' => NULL, 'created_at' => NULL, 'created_by' => NULL),
            array('id' => '93', 'TransID' => NULL, 'order_id' => NULL, 'status' => NULL, 'error_code' => NULL, 'created_at' => NULL, 'created_by' => NULL),
            array('id' => '94', 'TransID' => 'ATGDEMO92', 'order_id' => '523', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-07 11:28:41', 'created_by' => '90'),
            array('id' => '96', 'TransID' => 'ATGDEMO1', 'order_id' => NULL, 'status' => 'Error, You dont have any credit limits!', 'error_code' => '0', 'created_at' => '2017-11-07 13:34:57', 'created_by' => '90'),
            array('id' => '97', 'TransID' => 'ATGDEMO93', 'order_id' => '526', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-07 14:04:59', 'created_by' => '90'),
            array('id' => '98', 'TransID' => 'ATGDEMO94', 'order_id' => '527', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-07 14:10:51', 'created_by' => '90'),
            array('id' => '99', 'TransID' => 'ATGDEMO95', 'order_id' => NULL, 'status' => NULL, 'error_code' => NULL, 'created_at' => NULL, 'created_by' => NULL),
            array('id' => '100', 'TransID' => NULL, 'order_id' => '532', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-08 03:36:26', 'created_by' => 'testres1'),
            array('id' => '101', 'TransID' => NULL, 'order_id' => NULL, 'status' => NULL, 'error_code' => NULL, 'created_at' => NULL, 'created_by' => NULL),
            array('id' => '102', 'TransID' => NULL, 'order_id' => '533', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-08 03:50:59', 'created_by' => 'testres2'),
            array('id' => '103', 'TransID' => NULL, 'order_id' => '534', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-08 04:35:40', 'created_by' => '90'),
            array('id' => '104', 'TransID' => NULL, 'order_id' => '535', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-08 04:37:30', 'created_by' => 'testres2'),
            array('id' => '105', 'TransID' => 'ATGLOCAL2', 'order_id' => '536', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-08 07:08:35', 'created_by' => '90'),
            array('id' => '106', 'TransID' => 'ATGLOCAL20096', 'order_id' => '537', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-08 07:16:10', 'created_by' => '90'),
            array('id' => '107', 'TransID' => 'ATGLOCAL20097', 'order_id' => '538', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-08 07:16:36', 'created_by' => '90'),
            array('id' => '108', 'TransID' => 'ATGLOCAL3', 'order_id' => '539', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-08 09:10:55', 'created_by' => '90'),
            array('id' => '109', 'TransID' => 'ATGLOCAL4', 'order_id' => '540', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-08 11:45:23', 'created_by' => '90'),
            array('id' => '110', 'TransID' => 'ATGLOCAL5', 'order_id' => '541', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-08 11:47:11', 'created_by' => '90'),
            array('id' => '111', 'TransID' => 'ATGLOCAL6', 'order_id' => '548', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-09 07:32:30', 'created_by' => '90'),
            array('id' => '112', 'TransID' => 'ATGLOCAL20098', 'order_id' => '550', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-09 11:14:44', 'created_by' => '90'),
            array('id' => '113', 'TransID' => NULL, 'order_id' => '553', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-09 13:52:15', 'created_by' => '90'),
            array('id' => '114', 'TransID' => 'ATGLOCAL12', 'order_id' => '557', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-10 09:46:27', 'created_by' => '90'),
            array('id' => '115', 'TransID' => 'ATGLOCAL13', 'order_id' => '562', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-10 16:47:20', 'created_by' => '90'),
            array('id' => '116', 'TransID' => 'ATGLOCAL14', 'order_id' => '563', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-10 16:52:27', 'created_by' => '90'),
            array('id' => '117', 'TransID' => 'ATGDEMO39', 'order_id' => '570', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-11 04:00:00', 'created_by' => '90'),
            array('id' => '118', 'TransID' => 'ATGDEMO40', 'order_id' => NULL, 'status' => 'Tama Pay Order Not Found and/or Order May Be Processed!', 'error_code' => '0', 'created_at' => '2017-11-11 04:04:04', 'created_by' => '90'),
            array('id' => '119', 'TransID' => 'ATGDEMO41', 'order_id' => '571', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-11 04:16:41', 'created_by' => '90'),
            array('id' => '120', 'TransID' => 'ATGLOCAL15', 'order_id' => '572', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-11 04:35:24', 'created_by' => '90'),
            array('id' => '121', 'TransID' => 'ATGDEMO42', 'order_id' => '573', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-11 08:03:43', 'created_by' => '90'),
            array('id' => '122', 'TransID' => 'ATGDEMO43', 'order_id' => '574', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-11 08:09:04', 'created_by' => '90'),
            array('id' => '123', 'TransID' => 'ATGLOCAL20099', 'order_id' => '575', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-11 09:24:18', 'created_by' => '90'),
            array('id' => '124', 'TransID' => 'ATGDEMO44', 'order_id' => '576', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-11 11:17:09', 'created_by' => '90'),
            array('id' => '125', 'TransID' => 'ATGLOCAL200100', 'order_id' => '577', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-11 11:18:17', 'created_by' => '90'),
            array('id' => '126', 'TransID' => 'ATGLOCAL200101', 'order_id' => NULL, 'status' => NULL, 'error_code' => NULL, 'created_at' => NULL, 'created_by' => NULL),
            array('id' => '127', 'TransID' => 'ATGLOCAL200102', 'order_id' => NULL, 'status' => NULL, 'error_code' => NULL, 'created_at' => NULL, 'created_by' => NULL),
            array('id' => '128', 'TransID' => 'ATGLOCAL16', 'order_id' => '582', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-13 07:48:14', 'created_by' => '90'),
            array('id' => '129', 'TransID' => 'ATGLOCAL17', 'order_id' => NULL, 'status' => NULL, 'error_code' => NULL, 'created_at' => NULL, 'created_by' => NULL),
            array('id' => '130', 'TransID' => 'ATGDEMO45', 'order_id' => NULL, 'status' => NULL, 'error_code' => NULL, 'created_at' => NULL, 'created_by' => NULL),
            array('id' => '131', 'TransID' => 'ATGLOCAL18', 'order_id' => '583', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-13 08:26:51', 'created_by' => '90'),
            array('id' => '132', 'TransID' => 'ATGLOCAL19', 'order_id' => NULL, 'status' => NULL, 'error_code' => NULL, 'created_at' => NULL, 'created_by' => NULL),
            array('id' => '133', 'TransID' => 'ATGLOCAL46', 'order_id' => '590', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-14 16:48:43', 'created_by' => '90'),
            array('id' => '134', 'TransID' => 'ATGLOCAL47', 'order_id' => '591', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-14 16:57:49', 'created_by' => '90'),
            array('id' => '135', 'TransID' => 'ATGLOCAL48', 'order_id' => '598', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-15 09:35:14', 'created_by' => '90'),
            array('id' => '136', 'TransID' => 'XXXX000581', 'order_id' => '605', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-15 18:56:51', 'created_by' => '90'),
            array('id' => '137', 'TransID' => 'XXXX00051', 'order_id' => '606', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-15 19:12:03', 'created_by' => '90'),
            array('id' => '138', 'TransID' => 'ATGLOCAL49', 'order_id' => '607', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-16 10:45:33', 'created_by' => 'atgmobile_test'),
            array('id' => '139', 'TransID' => 'ATGDE_1', 'order_id' => '608', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-16 11:03:57', 'created_by' => '90'),
            array('id' => '140', 'TransID' => 'ATGDE_2', 'order_id' => '609', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-16 11:40:11', 'created_by' => '90'),
            array('id' => '141', 'TransID' => NULL, 'order_id' => '641', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-20 23:34:44', 'created_by' => 'testres2'),
            array('id' => '142', 'TransID' => NULL, 'order_id' => '649', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-22 16:51:00', 'created_by' => '90'),
            array('id' => '143', 'TransID' => NULL, 'order_id' => '663', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-24 23:38:38', 'created_by' => 'sivaguru'),
            array('id' => '144', 'TransID' => NULL, 'order_id' => '664', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-25 00:04:04', 'created_by' => '90'),
            array('id' => '145', 'TransID' => NULL, 'order_id' => '665', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-25 00:07:59', 'created_by' => 'testmgr1'),
            array('id' => '146', 'TransID' => NULL, 'order_id' => '666', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-25 00:14:22', 'created_by' => 'testmgr1'),
            array('id' => '147', 'TransID' => NULL, 'order_id' => '667', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-25 00:17:35', 'created_by' => 'sivaguru'),
            array('id' => '148', 'TransID' => NULL, 'order_id' => '680', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-26 14:55:12', 'created_by' => 'manager1'),
            array('id' => '149', 'TransID' => NULL, 'order_id' => '682', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-26 15:30:47', 'created_by' => 'testmgr1'),
            array('id' => '150', 'TransID' => 'ATGDE_3', 'order_id' => '683', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-27 07:12:27', 'created_by' => '90'),
            array('id' => '151', 'TransID' => 'ATGDE_4', 'order_id' => '684', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-27 07:41:49', 'created_by' => '90'),
            array('id' => '152', 'TransID' => NULL, 'order_id' => '705', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-28 17:39:12', 'created_by' => 'sivaguru'),
            array('id' => '153', 'TransID' => NULL, 'order_id' => '712', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-28 23:18:31', 'created_by' => 'sivaguru'),
            array('id' => '154', 'TransID' => NULL, 'order_id' => '714', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-11-28 23:21:13', 'created_by' => '90'),
            array('id' => '155', 'TransID' => NULL, 'order_id' => '780', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-12-07 17:41:39', 'created_by' => 'pms'),
            array('id' => '156', 'TransID' => NULL, 'order_id' => '781', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-12-07 17:47:40', 'created_by' => 'HOUSSEN'),
            array('id' => '157', 'TransID' => NULL, 'order_id' => '783', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-12-07 17:52:20', 'created_by' => 'ADIL SERVICE'),
            array('id' => '158', 'TransID' => NULL, 'order_id' => '819', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-12-10 17:15:45', 'created_by' => 'TEST'),
            array('id' => '159', 'TransID' => 'ATGDE_5', 'order_id' => '823', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2017-12-11 11:43:15', 'created_by' => '90'),
            array('id' => '160', 'TransID' => NULL, 'order_id' => NULL, 'status' => NULL, 'error_code' => NULL, 'created_at' => NULL, 'created_by' => NULL),
            array('id' => '161', 'TransID' => NULL, 'order_id' => '1252', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-01-23 05:24:42', 'created_by' => 'balajim'),
            array('id' => '162', 'TransID' => NULL, 'order_id' => NULL, 'status' => NULL, 'error_code' => NULL, 'created_at' => NULL, 'created_by' => NULL),
            array('id' => '163', 'TransID' => NULL, 'order_id' => '1795', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-02-10 12:33:58', 'created_by' => 'marche'),
            array('id' => '164', 'TransID' => NULL, 'order_id' => NULL, 'status' => NULL, 'error_code' => NULL, 'created_at' => NULL, 'created_by' => NULL),
            array('id' => '165', 'TransID' => NULL, 'order_id' => '1931', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-02-11 14:02:19', 'created_by' => 'WATELECOM'),
            array('id' => '166', 'TransID' => NULL, 'order_id' => NULL, 'status' => NULL, 'error_code' => NULL, 'created_at' => NULL, 'created_by' => NULL),
            array('id' => '167', 'TransID' => NULL, 'order_id' => NULL, 'status' => NULL, 'error_code' => NULL, 'created_at' => NULL, 'created_by' => NULL),
            array('id' => '168', 'TransID' => NULL, 'order_id' => NULL, 'status' => NULL, 'error_code' => NULL, 'created_at' => NULL, 'created_by' => NULL),
            array('id' => '169', 'TransID' => NULL, 'order_id' => NULL, 'status' => NULL, 'error_code' => NULL, 'created_at' => NULL, 'created_by' => NULL),
            array('id' => '170', 'TransID' => NULL, 'order_id' => NULL, 'status' => NULL, 'error_code' => NULL, 'created_at' => NULL, 'created_by' => NULL),
            array('id' => '171', 'TransID' => NULL, 'order_id' => '2020', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-02-12 11:30:17', 'created_by' => 'pms'),
            array('id' => '172', 'TransID' => NULL, 'order_id' => '2021', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-02-12 11:45:02', 'created_by' => 'pms'),
            array('id' => '173', 'TransID' => NULL, 'order_id' => '2043', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-02-12 12:56:01', 'created_by' => 'CYBERCITY13'),
            array('id' => '174', 'TransID' => NULL, 'order_id' => '2078', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-02-12 17:40:13', 'created_by' => 'CYBERCITY13'),
            array('id' => '175', 'TransID' => NULL, 'order_id' => '2507', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-02-14 20:22:48', 'created_by' => 'CYBERCITY13'),
            array('id' => '176', 'TransID' => NULL, 'order_id' => '2553', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-02-15 14:32:59', 'created_by' => 'sarldefrance'),
            array('id' => '177', 'TransID' => NULL, 'order_id' => '2556', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-02-15 14:35:06', 'created_by' => 'sarldefrance'),
            array('id' => '178', 'TransID' => NULL, 'order_id' => '2559', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-02-15 14:37:40', 'created_by' => 'sarldefrance'),
            array('id' => '179', 'TransID' => NULL, 'order_id' => '2648', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-02-15 19:42:09', 'created_by' => 'CYBERARLESIENS'),
            array('id' => '180', 'TransID' => NULL, 'order_id' => '2735', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-02-16 12:56:51', 'created_by' => 'CYBERARLESIENS'),
            array('id' => '181', 'TransID' => NULL, 'order_id' => '2784', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-02-16 16:34:59', 'created_by' => 'CYBERARLESIENS'),
            array('id' => '182', 'TransID' => NULL, 'order_id' => '2910', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-02-17 13:18:21', 'created_by' => 'VOIPSERVICES'),
            array('id' => '183', 'TransID' => NULL, 'order_id' => '3123', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-02-18 12:51:45', 'created_by' => 'CYBERARLESIENS'),
            array('id' => '184', 'TransID' => NULL, 'order_id' => '3143', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-02-18 14:01:18', 'created_by' => 'CYBERARLESIENS'),
            array('id' => '185', 'TransID' => NULL, 'order_id' => '3400', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-02-19 14:53:34', 'created_by' => 'VOIPSERVICES'),
            array('id' => '186', 'TransID' => NULL, 'order_id' => '3403', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-02-19 15:04:07', 'created_by' => 'VOIPSERVICES'),
            array('id' => '187', 'TransID' => NULL, 'order_id' => '3599', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-02-20 11:57:19', 'created_by' => 'chouaib'),
            array('id' => '188', 'TransID' => NULL, 'order_id' => '4221', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-02-23 20:06:32', 'created_by' => 'CYBERARLESIENS'),
            array('id' => '189', 'TransID' => NULL, 'order_id' => '4477', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-02-25 11:07:21', 'created_by' => 'ODDO'),
            array('id' => '190', 'TransID' => NULL, 'order_id' => '4490', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-02-25 11:40:36', 'created_by' => 'CYBERARLESIENS'),
            array('id' => '191', 'TransID' => NULL, 'order_id' => '4547', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-02-25 15:40:30', 'created_by' => 'PHONENET'),
            array('id' => '192', 'TransID' => NULL, 'order_id' => '4879', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-02-26 15:56:39', 'created_by' => 'ACBM13'),
            array('id' => '193', 'TransID' => NULL, 'order_id' => '5249', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-02-27 18:13:02', 'created_by' => 'WATELECOM'),
            array('id' => '194', 'TransID' => NULL, 'order_id' => '5314', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-02-27 20:04:16', 'created_by' => 'EUROPHONE'),
            array('id' => '195', 'TransID' => NULL, 'order_id' => '5798', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-01 14:38:28', 'created_by' => 'CYBERSTGILES'),
            array('id' => '196', 'TransID' => NULL, 'order_id' => '5852', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-01 16:10:54', 'created_by' => 'sarldefrance'),
            array('id' => '197', 'TransID' => NULL, 'order_id' => '6071', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-02 14:57:31', 'created_by' => 'CYBERSTGILES'),
            array('id' => '198', 'TransID' => NULL, 'order_id' => '6074', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-02 14:59:03', 'created_by' => 'CYBERSTGILES'),
            array('id' => '199', 'TransID' => NULL, 'order_id' => '6083', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-02 15:04:31', 'created_by' => 'CYBERSTGILES'),
            array('id' => '200', 'TransID' => NULL, 'order_id' => '6146', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-02 17:37:30', 'created_by' => 'CYBERSTGILES'),
            array('id' => '201', 'TransID' => NULL, 'order_id' => '6184', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-02 18:49:22', 'created_by' => 'cybercity13200'),
            array('id' => '202', 'TransID' => NULL, 'order_id' => '6203', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-02 19:14:40', 'created_by' => 'CYBERSTGILES'),
            array('id' => '203', 'TransID' => NULL, 'order_id' => '6241', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-03 09:12:18', 'created_by' => 'CYBERARLESIENS'),
            array('id' => '204', 'TransID' => NULL, 'order_id' => '6304', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-03 12:11:54', 'created_by' => 'ARKANE'),
            array('id' => '205', 'TransID' => NULL, 'order_id' => '6336', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-03 13:34:56', 'created_by' => 'CYBERARLESIENS'),
            array('id' => '206', 'TransID' => NULL, 'order_id' => '6436', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-03 18:02:18', 'created_by' => 'ACBM13'),
            array('id' => '207', 'TransID' => NULL, 'order_id' => '6629', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-04 12:20:10', 'created_by' => 'CYBERARLESIENS'),
            array('id' => '208', 'TransID' => NULL, 'order_id' => '6745', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-04 17:37:26', 'created_by' => 'Hitech'),
            array('id' => '209', 'TransID' => NULL, 'order_id' => '6862', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-05 10:40:56', 'created_by' => 'cybercity13200'),
            array('id' => '210', 'TransID' => NULL, 'order_id' => '6945', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-05 13:39:44', 'created_by' => 'PHONENET'),
            array('id' => '211', 'TransID' => NULL, 'order_id' => '6952', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-05 13:50:37', 'created_by' => 'VOIPSERVICES'),
            array('id' => '212', 'TransID' => NULL, 'order_id' => '6955', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-05 13:52:10', 'created_by' => 'VOIPSERVICES'),
            array('id' => '213', 'TransID' => NULL, 'order_id' => '6958', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-05 13:53:12', 'created_by' => 'VOIPSERVICES'),
            array('id' => '214', 'TransID' => NULL, 'order_id' => '7118', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-05 20:27:32', 'created_by' => 'cybercity13200'),
            array('id' => '215', 'TransID' => NULL, 'order_id' => '7121', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-05 20:28:33', 'created_by' => 'cybercity13200'),
            array('id' => '216', 'TransID' => NULL, 'order_id' => '7376', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-06 17:47:35', 'created_by' => 'Hitech'),
            array('id' => '217', 'TransID' => NULL, 'order_id' => '7647', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-07 15:25:26', 'created_by' => 'VOIPSERVICES'),
            array('id' => '218', 'TransID' => NULL, 'order_id' => '8007', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-08 16:20:25', 'created_by' => 'WATELECOM'),
            array('id' => '219', 'TransID' => NULL, 'order_id' => '8075', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-08 18:52:04', 'created_by' => 'PHONENET'),
            array('id' => '220', 'TransID' => NULL, 'order_id' => '8236', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-09 12:30:22', 'created_by' => 'BENICOM'),
            array('id' => '221', 'TransID' => NULL, 'order_id' => '8263', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-09 15:25:08', 'created_by' => 'Hitech'),
            array('id' => '222', 'TransID' => NULL, 'order_id' => '8328', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-09 17:21:14', 'created_by' => 'picon'),
            array('id' => '223', 'TransID' => NULL, 'order_id' => '8350', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-09 18:14:12', 'created_by' => 'BENICOM'),
            array('id' => '224', 'TransID' => NULL, 'order_id' => '8488', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-10 11:56:19', 'created_by' => 'BENICOM'),
            array('id' => '225', 'TransID' => NULL, 'order_id' => '8549', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-10 14:03:54', 'created_by' => 'eroufi'),
            array('id' => '226', 'TransID' => NULL, 'order_id' => '8575', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-10 14:58:40', 'created_by' => 'cybercity13200'),
            array('id' => '227', 'TransID' => NULL, 'order_id' => '8619', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-10 16:53:35', 'created_by' => 'cybercity13200'),
            array('id' => '228', 'TransID' => NULL, 'order_id' => '8622', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-10 16:55:39', 'created_by' => 'cybercity13200'),
            array('id' => '229', 'TransID' => NULL, 'order_id' => '8625', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-10 16:56:57', 'created_by' => 'cybercity13200'),
            array('id' => '230', 'TransID' => NULL, 'order_id' => '8628', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-10 17:00:24', 'created_by' => 'sarldefrance'),
            array('id' => '231', 'TransID' => NULL, 'order_id' => '8633', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-10 17:04:07', 'created_by' => 'sarldefrance'),
            array('id' => '232', 'TransID' => NULL, 'order_id' => '8741', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-10 19:45:49', 'created_by' => 'Hitech'),
            array('id' => '233', 'TransID' => NULL, 'order_id' => '8815', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-11 10:53:58', 'created_by' => 'BENICOM'),
            array('id' => '234', 'TransID' => NULL, 'order_id' => '8836', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-11 11:30:50', 'created_by' => 'BENICOM'),
            array('id' => '235', 'TransID' => NULL, 'order_id' => '8963', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-11 16:21:57', 'created_by' => 'CYBERARLESIENS'),
            array('id' => '236', 'TransID' => NULL, 'order_id' => '8974', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-11 16:24:39', 'created_by' => 'CYBERARLESIENS'),
            array('id' => '237', 'TransID' => NULL, 'order_id' => '9130', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-11 19:40:39', 'created_by' => 'PHONENET'),
            array('id' => '238', 'TransID' => NULL, 'order_id' => '9529', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-12 20:49:40', 'created_by' => 'cybercity13200'),
            array('id' => '239', 'TransID' => NULL, 'order_id' => '9578', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-13 10:38:26', 'created_by' => 'eroufi'),
            array('id' => '240', 'TransID' => NULL, 'order_id' => '9595', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-13 11:24:55', 'created_by' => 'chouaib'),
            array('id' => '241', 'TransID' => NULL, 'order_id' => '9600', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-13 11:29:00', 'created_by' => 'PHONENET'),
            array('id' => '242', 'TransID' => NULL, 'order_id' => '9771', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-13 19:23:13', 'created_by' => 'PHONENET'),
            array('id' => '243', 'TransID' => NULL, 'order_id' => '9776', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-13 19:27:16', 'created_by' => 'PHONENET'),
            array('id' => '244', 'TransID' => NULL, 'order_id' => '9803', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-13 19:50:41', 'created_by' => 'Hitech'),
            array('id' => '245', 'TransID' => NULL, 'order_id' => '10664', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-16 14:01:40', 'created_by' => 'VOIPSERVICES'),
            array('id' => '246', 'TransID' => NULL, 'order_id' => '10671', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-16 14:02:49', 'created_by' => 'VOIPSERVICES'),
            array('id' => '247', 'TransID' => NULL, 'order_id' => '10676', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-16 14:04:05', 'created_by' => 'VOIPSERVICES'),
            array('id' => '248', 'TransID' => NULL, 'order_id' => '10681', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-16 14:06:25', 'created_by' => 'VOIPSERVICES'),
            array('id' => '249', 'TransID' => NULL, 'order_id' => '10984', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-17 10:22:11', 'created_by' => 'sarldefrance'),
            array('id' => '250', 'TransID' => NULL, 'order_id' => '11148', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-17 15:49:48', 'created_by' => 'CYBERARLESIENS'),
            array('id' => '251', 'TransID' => NULL, 'order_id' => '11244', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-17 18:08:24', 'created_by' => 'PHONENET'),
            array('id' => '252', 'TransID' => NULL, 'order_id' => '11277', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-17 19:03:11', 'created_by' => 'BENICOM'),
            array('id' => '253', 'TransID' => NULL, 'order_id' => '11294', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-17 19:40:10', 'created_by' => 'CYBERARLESIENS'),
            array('id' => '254', 'TransID' => NULL, 'order_id' => '11307', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-17 19:51:55', 'created_by' => 'Hitech'),
            array('id' => '255', 'TransID' => NULL, 'order_id' => '11314', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-17 20:07:17', 'created_by' => 'CYBERARLESIENS'),
            array('id' => '256', 'TransID' => NULL, 'order_id' => '11395', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-18 11:28:40', 'created_by' => 'BENICOM'),
            array('id' => '257', 'TransID' => NULL, 'order_id' => '11525', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-18 15:27:09', 'created_by' => 'cybercity13200'),
            array('id' => '258', 'TransID' => NULL, 'order_id' => '11587', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-18 17:37:06', 'created_by' => 'Hitech'),
            array('id' => '259', 'TransID' => NULL, 'order_id' => '11591', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-18 17:55:54', 'created_by' => 'CYBERARLESIENS'),
            array('id' => '260', 'TransID' => NULL, 'order_id' => '11651', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-18 18:58:59', 'created_by' => 'CYBERARLESIENS'),
            array('id' => '261', 'TransID' => NULL, 'order_id' => '11664', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-18 19:08:13', 'created_by' => 'Hitech'),
            array('id' => '262', 'TransID' => NULL, 'order_id' => NULL, 'status' => 'Error, Unable to topup now!', 'error_code' => '0', 'created_at' => '2018-03-19 18:52:48', 'created_by' => 'sarldefrance'),
            array('id' => '263', 'TransID' => NULL, 'order_id' => NULL, 'status' => 'Error, Unable to topup now!', 'error_code' => '0', 'created_at' => '2018-03-19 18:54:48', 'created_by' => 'sarldefrance'),
            array('id' => '264', 'TransID' => NULL, 'order_id' => '11947', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-19 18:56:01', 'created_by' => 'sarldefrance'),
            array('id' => '265', 'TransID' => NULL, 'order_id' => '11950', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-19 18:57:45', 'created_by' => 'sarldefrance'),
            array('id' => '266', 'TransID' => NULL, 'order_id' => '12190', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-20 15:36:56', 'created_by' => 'BENICOM'),
            array('id' => '267', 'TransID' => NULL, 'order_id' => '12289', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-20 19:15:07', 'created_by' => 'ODDO'),
            array('id' => '268', 'TransID' => NULL, 'order_id' => '12524', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-21 18:52:33', 'created_by' => 'ODDO'),
            array('id' => '269', 'TransID' => NULL, 'order_id' => '12722', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-22 14:26:44', 'created_by' => 'BENICOM'),
            array('id' => '270', 'TransID' => NULL, 'order_id' => NULL, 'status' => NULL, 'error_code' => NULL, 'created_at' => NULL, 'created_by' => NULL),
            array('id' => '271', 'TransID' => NULL, 'order_id' => NULL, 'status' => 'Error, Unable to topup now!', 'error_code' => '0', 'created_at' => '2018-03-22 18:12:30', 'created_by' => 'ACBM13'),
            array('id' => '272', 'TransID' => NULL, 'order_id' => NULL, 'status' => 'Error, Unable to topup now!', 'error_code' => '0', 'created_at' => '2018-03-22 18:13:25', 'created_by' => 'ACBM13'),
            array('id' => '273', 'TransID' => NULL, 'order_id' => '12922', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-23 11:24:23', 'created_by' => 'Hitech'),
            array('id' => '274', 'TransID' => NULL, 'order_id' => '12929', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-23 11:26:22', 'created_by' => 'Hitech'),
            array('id' => '275', 'TransID' => NULL, 'order_id' => '12932', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-23 11:32:01', 'created_by' => 'Hitech'),
            array('id' => '276', 'TransID' => NULL, 'order_id' => '12988', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-23 15:51:32', 'created_by' => 'Hitech'),
            array('id' => '277', 'TransID' => NULL, 'order_id' => '12994', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-23 15:59:28', 'created_by' => 'PHONENET'),
            array('id' => '278', 'TransID' => NULL, 'order_id' => '13300', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-24 12:39:47', 'created_by' => 'BENICOM'),
            array('id' => '279', 'TransID' => NULL, 'order_id' => '13333', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-24 13:27:39', 'created_by' => 'eroufi'),
            array('id' => '280', 'TransID' => 'ATGDE_6', 'order_id' => '13365', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-24 14:35:35', 'created_by' => '90'),
            array('id' => '281', 'TransID' => NULL, 'order_id' => '13391', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-24 15:34:28', 'created_by' => 'cybercity13200'),
            array('id' => '282', 'TransID' => NULL, 'order_id' => '13540', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-24 19:04:52', 'created_by' => 'BENICOM'),
            array('id' => '283', 'TransID' => NULL, 'order_id' => '13689', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-25 12:53:45', 'created_by' => 'CYBERARLESIENS'),
            array('id' => '284', 'TransID' => NULL, 'order_id' => '13708', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-25 13:24:50', 'created_by' => 'eroufi'),
            array('id' => '285', 'TransID' => NULL, 'order_id' => '13824', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-25 16:37:48', 'created_by' => 'eroufi'),
            array('id' => '286', 'TransID' => NULL, 'order_id' => '13848', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-25 17:32:51', 'created_by' => 'CYBERARLESIENS'),
            array('id' => '287', 'TransID' => NULL, 'order_id' => '13851', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-25 17:34:05', 'created_by' => 'CYBERARLESIENS'),
            array('id' => '288', 'TransID' => NULL, 'order_id' => '13854', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-25 17:36:07', 'created_by' => 'CYBERSTGILES'),
            array('id' => '289', 'TransID' => NULL, 'order_id' => '13903', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-25 18:19:03', 'created_by' => 'CYBERARLESIENS'),
            array('id' => '290', 'TransID' => NULL, 'order_id' => '13910', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-25 18:27:18', 'created_by' => 'eroufi'),
            array('id' => '291', 'TransID' => NULL, 'order_id' => '13925', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-25 19:00:56', 'created_by' => 'CYBERARLESIENS'),
            array('id' => '292', 'TransID' => NULL, 'order_id' => '13928', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-25 19:03:32', 'created_by' => 'CYBERARLESIENS'),
            array('id' => '293', 'TransID' => NULL, 'order_id' => '13957', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-25 19:52:25', 'created_by' => 'Hitech'),
            array('id' => '294', 'TransID' => NULL, 'order_id' => '13980', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-25 20:34:36', 'created_by' => 'Hitech'),
            array('id' => '295', 'TransID' => NULL, 'order_id' => '14118', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-26 11:59:20', 'created_by' => 'eroufi'),
            array('id' => '296', 'TransID' => NULL, 'order_id' => NULL, 'status' => 'Error, Unable to topup now!', 'error_code' => '0', 'created_at' => '2018-03-26 12:06:04', 'created_by' => 'CYBERARLESIENS'),
            array('id' => '297', 'TransID' => NULL, 'order_id' => '14220', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-26 14:00:42', 'created_by' => 'VOIPSERVICES'),
            array('id' => '298', 'TransID' => NULL, 'order_id' => NULL, 'status' => 'Error, Unable to topup now!', 'error_code' => '0', 'created_at' => '2018-03-27 11:54:03', 'created_by' => 'eroufi'),
            array('id' => '299', 'TransID' => NULL, 'order_id' => '14797', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-27 20:12:34', 'created_by' => 'cybercity13200'),
            array('id' => '300', 'TransID' => NULL, 'order_id' => '14909', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-28 11:54:24', 'created_by' => 'BENICOM'),
            array('id' => '301', 'TransID' => NULL, 'order_id' => '15076', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-28 17:52:22', 'created_by' => 'BENICOM'),
            array('id' => '302', 'TransID' => NULL, 'order_id' => '15085', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-28 18:03:59', 'created_by' => 'BENICOM'),
            array('id' => '303', 'TransID' => NULL, 'order_id' => '15108', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-28 18:25:18', 'created_by' => 'BENICOM'),
            array('id' => '304', 'TransID' => NULL, 'order_id' => '15167', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-28 19:22:55', 'created_by' => 'sarldefrance'),
            array('id' => '305', 'TransID' => NULL, 'order_id' => '15204', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-28 20:13:12', 'created_by' => 'PHONENET'),
            array('id' => '306', 'TransID' => NULL, 'order_id' => '15246', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-28 21:39:19', 'created_by' => 'cybercity13200'),
            array('id' => '307', 'TransID' => NULL, 'order_id' => '15299', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-29 10:45:50', 'created_by' => 'Hitech'),
            array('id' => '308', 'TransID' => 'ATGDE_7', 'order_id' => '15384', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-29 13:27:54', 'created_by' => '90'),
            array('id' => '309', 'TransID' => NULL, 'order_id' => '15410', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-29 14:18:08', 'created_by' => 'ACBM13'),
            array('id' => '310', 'TransID' => NULL, 'order_id' => '15512', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-29 18:00:38', 'created_by' => 'Hitech'),
            array('id' => '311', 'TransID' => NULL, 'order_id' => '15743', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-30 09:59:13', 'created_by' => 'coiffure'),
            array('id' => '312', 'TransID' => NULL, 'order_id' => '15864', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-30 12:40:14', 'created_by' => 'PHONENET'),
            array('id' => '313', 'TransID' => NULL, 'order_id' => '16150', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-30 19:03:30', 'created_by' => 'ACBM13'),
            array('id' => '314', 'TransID' => NULL, 'order_id' => '16158', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-30 19:06:09', 'created_by' => 'ACBM13'),
            array('id' => '315', 'TransID' => NULL, 'order_id' => '16174', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-30 19:27:08', 'created_by' => 'brahim'),
            array('id' => '316', 'TransID' => NULL, 'order_id' => '16177', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-30 19:28:07', 'created_by' => 'brahim'),
            array('id' => '317', 'TransID' => NULL, 'order_id' => '16464', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-31 13:19:12', 'created_by' => 'CYBERARLESIENS'),
            array('id' => '318', 'TransID' => NULL, 'order_id' => '16489', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-31 14:27:07', 'created_by' => 'brahim'),
            array('id' => '319', 'TransID' => NULL, 'order_id' => '16496', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-31 14:29:51', 'created_by' => 'brahim'),
            array('id' => '320', 'TransID' => NULL, 'order_id' => '16557', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-31 15:34:49', 'created_by' => 'CYBERSTGILES'),
            array('id' => '321', 'TransID' => NULL, 'order_id' => '16560', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-31 15:37:08', 'created_by' => 'CYBERSTGILES'),
            array('id' => '322', 'TransID' => NULL, 'order_id' => '16572', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-31 15:57:51', 'created_by' => 'CYBERSTGILES'),
            array('id' => '323', 'TransID' => NULL, 'order_id' => '16585', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-31 16:07:24', 'created_by' => 'Abdelwahed'),
            array('id' => '324', 'TransID' => NULL, 'order_id' => '16675', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-31 17:46:44', 'created_by' => 'brahim'),
            array('id' => '325', 'TransID' => NULL, 'order_id' => '16740', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-03-31 18:55:07', 'created_by' => 'brahim'),
            array('id' => '326', 'TransID' => NULL, 'order_id' => '16907', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-04-01 10:40:53', 'created_by' => 'CYBERARLESIENS'),
            array('id' => '327', 'TransID' => NULL, 'order_id' => NULL, 'status' => 'Error, Unable to topup now!', 'error_code' => '0', 'created_at' => '2018-04-01 10:54:02', 'created_by' => 'CYBERARLESIENS'),
            array('id' => '328', 'TransID' => NULL, 'order_id' => '16918', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-04-01 10:54:48', 'created_by' => 'CYBERARLESIENS'),
            array('id' => '329', 'TransID' => NULL, 'order_id' => '16921', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-04-01 10:56:25', 'created_by' => 'CYBERARLESIENS'),
            array('id' => '330', 'TransID' => NULL, 'order_id' => '16994', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-04-01 12:15:06', 'created_by' => 'BENICOM'),
            array('id' => '331', 'TransID' => NULL, 'order_id' => '17038', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-04-01 12:55:33', 'created_by' => 'CYBERARLESIENS'),
            array('id' => '332', 'TransID' => NULL, 'order_id' => '17116', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-04-01 15:06:11', 'created_by' => 'Hitech'),
            array('id' => '333', 'TransID' => NULL, 'order_id' => '17119', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-04-01 15:07:29', 'created_by' => 'eroufi'),
            array('id' => '334', 'TransID' => NULL, 'order_id' => '17274', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-04-01 18:18:51', 'created_by' => 'Hitech'),
            array('id' => '335', 'TransID' => NULL, 'order_id' => '17277', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-04-01 18:23:35', 'created_by' => 'brahim'),
            array('id' => '336', 'TransID' => NULL, 'order_id' => '17364', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-04-01 19:33:36', 'created_by' => 'CYBERARLESIENS'),
            array('id' => '337', 'TransID' => NULL, 'order_id' => '17481', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-04-01 21:38:16', 'created_by' => 'CYBERARLESIENS'),
            array('id' => '338', 'TransID' => NULL, 'order_id' => '17493', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-04-01 21:59:30', 'created_by' => 'PHONENET'),
            array('id' => '339', 'TransID' => NULL, 'order_id' => '17498', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-04-01 22:04:31', 'created_by' => 'cybercity13200'),
            array('id' => '340', 'TransID' => NULL, 'order_id' => '17696', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-04-02 12:31:58', 'created_by' => 'eroufi'),
            array('id' => '341', 'TransID' => NULL, 'order_id' => '17733', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-04-02 13:29:27', 'created_by' => 'cybercity13200'),
            array('id' => '342', 'TransID' => NULL, 'order_id' => '17742', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-04-02 13:41:33', 'created_by' => 'cybercity13200'),
            array('id' => '343', 'TransID' => NULL, 'order_id' => '17799', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-04-02 14:52:23', 'created_by' => 'eroufi'),
            array('id' => '344', 'TransID' => NULL, 'order_id' => '17860', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-04-02 16:26:51', 'created_by' => 'Hitech'),
            array('id' => '345', 'TransID' => NULL, 'order_id' => '17869', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-04-02 16:30:20', 'created_by' => 'Hitech'),
            array('id' => '346', 'TransID' => NULL, 'order_id' => '17886', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-04-02 16:55:12', 'created_by' => 'eroufi'),
            array('id' => '347', 'TransID' => NULL, 'order_id' => '17895', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-04-02 17:16:36', 'created_by' => 'Hitech'),
            array('id' => '348', 'TransID' => NULL, 'order_id' => '17954', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-04-02 18:27:05', 'created_by' => 'Hitech'),
            array('id' => '349', 'TransID' => NULL, 'order_id' => '17977', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-04-02 18:48:50', 'created_by' => 'eroufi'),
            array('id' => '350', 'TransID' => NULL, 'order_id' => NULL, 'status' => 'Error, Unable to topup now!', 'error_code' => '0', 'created_at' => '2018-04-02 21:17:30', 'created_by' => 'brahim'),
            array('id' => '351', 'TransID' => NULL, 'order_id' => NULL, 'status' => 'Error, Unable to topup now!', 'error_code' => '0', 'created_at' => '2018-04-02 21:18:19', 'created_by' => 'brahim'),
            array('id' => '352', 'TransID' => NULL, 'order_id' => NULL, 'status' => 'Error, Unable to topup now!', 'error_code' => '0', 'created_at' => '2018-04-02 21:21:57', 'created_by' => 'brahim'),
            array('id' => '353', 'TransID' => NULL, 'order_id' => NULL, 'status' => 'Error, Unable to topup now!', 'error_code' => '0', 'created_at' => '2018-04-03 10:56:50', 'created_by' => 'zohir'),
            array('id' => '354', 'TransID' => NULL, 'order_id' => NULL, 'status' => 'Error, Unable to topup now!', 'error_code' => '0', 'created_at' => '2018-04-03 11:05:33', 'created_by' => 'zohir'),
            array('id' => '355', 'TransID' => NULL, 'order_id' => '18621', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-04-03 18:32:13', 'created_by' => 'eroufi'),
            array('id' => '356', 'TransID' => NULL, 'order_id' => '18725', 'status' => 'Order placed!', 'error_code' => '1', 'created_at' => '2018-04-03 20:26:38', 'created_by' => 'cybercity13200')
        );
        foreach ($tb_track_status as $row) {
            if (isset($row['order_id']) && Order::find($row['order_id']) && $row['created_by'] == 90)
                TrackStatus::insert($row);
        }
        return true;
    }

    static function migrate_user_access()
    {
        $tb_users_access = array(
            array('user_id' => '85', 'access_data' => '+ON9fCiNaZTAFQVCo63Ds0Yt6Rq5vfne1ptTJhiJ+tYIejOt8556/R178sj/ZbNP7Gnn8fHsf21y3y9bNgAKqVDctsjuQ7H8RbCS9ppZgdn8I/NaFXFHYxJeLSa5GbAEmGrAdvUhcVeRiz7+/9WyUV0AmgvA9MZlCETiL4mAn/U=', 'created_at' => '2017-11-07 08:47:09', 'created_by' => '1', 'updated_at' => '2018-04-13 16:45:45', 'updated_by' => '46'),
            array('user_id' => '124', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-01-08 13:22:39', 'created_by' => '85', 'updated_at' => '2018-03-26 16:06:16', 'updated_by' => '85'),
            array('user_id' => '125', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-01-08 13:32:52', 'created_by' => '124', 'updated_at' => '2018-04-08 17:09:38', 'updated_by' => '124'),
            array('user_id' => '126', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-01-10 13:06:45', 'created_by' => '124', 'updated_at' => '2018-02-08 19:51:12', 'updated_by' => '124'),
            array('user_id' => '127', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-01-15 18:00:13', 'created_by' => '124', 'updated_at' => '2018-03-16 11:29:54', 'updated_by' => '124'),
            array('user_id' => '131', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-01-15 18:25:29', 'created_by' => '124', 'updated_at' => '2018-03-05 10:24:55', 'updated_by' => '124'),
            array('user_id' => '132', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-01-16 19:17:53', 'created_by' => '124', 'updated_at' => '2018-04-08 10:54:27', 'updated_by' => '124'),
            array('user_id' => '134', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-01-16 21:55:37', 'created_by' => '85', 'updated_at' => '2018-02-24 10:22:19', 'updated_by' => '124'),
            array('user_id' => '140', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-01-23 16:48:24', 'created_by' => '124', 'updated_at' => '2018-04-19 11:49:11', 'updated_by' => '124'),
            array('user_id' => '142', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-01-25 12:05:32', 'created_by' => '85', 'updated_at' => '2018-04-12 14:20:04', 'updated_by' => '85'),
            array('user_id' => '143', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-01-25 13:34:39', 'created_by' => '124', 'updated_at' => '2018-04-23 10:31:19', 'updated_by' => '124'),
            array('user_id' => '147', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-01-28 16:10:38', 'created_by' => '85', 'updated_at' => '2018-04-05 19:21:12', 'updated_by' => '85'),
            array('user_id' => '148', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-01-28 16:12:30', 'created_by' => '85', 'updated_at' => NULL, 'updated_by' => NULL),
            array('user_id' => '157', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-01-28 23:13:52', 'created_by' => '147', 'updated_at' => '2018-04-16 16:03:07', 'updated_by' => '147'),
            array('user_id' => '161', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-01-31 12:36:37', 'created_by' => '142', 'updated_at' => '2018-04-04 10:33:25', 'updated_by' => '142'),
            array('user_id' => '162', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-01-31 13:00:20', 'created_by' => '142', 'updated_at' => '2018-04-02 18:33:32', 'updated_by' => '142'),
            array('user_id' => '163', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-02-02 11:06:31', 'created_by' => '124', 'updated_at' => '2018-02-17 12:46:08', 'updated_by' => '124'),
            array('user_id' => '165', 'access_data' => 'nW1KfTpeCgsmTzvNnzWw2HnwOPvvYWBSTjGRnGd07xTfDq/pwXrj2tIIFWiL+ssBffCpv8U0Edskiz4HiU1LiFubLKK+wkdfD+i1xxmcs8OsrCeryMvbrrY9L5F8mSa56Y74UEfcf0c2zm/HZf4SKxenFANy9JDuI/Egi+Iqyj8=', 'created_at' => '2018-02-03 13:07:19', 'created_by' => '124', 'updated_at' => NULL, 'updated_by' => NULL),
            array('user_id' => '166', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-02-06 12:46:30', 'created_by' => '142', 'updated_at' => '2018-04-23 18:58:37', 'updated_by' => '142'),
            array('user_id' => '169', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-02-07 12:09:42', 'created_by' => '85', 'updated_at' => '2018-03-29 13:50:36', 'updated_by' => '85'),
            array('user_id' => '170', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-02-07 12:12:11', 'created_by' => '142', 'updated_at' => '2018-04-16 14:44:39', 'updated_by' => '142'),
            array('user_id' => '171', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-02-08 16:16:49', 'created_by' => '169', 'updated_at' => '2018-02-28 17:00:15', 'updated_by' => '169'),
            array('user_id' => '172', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-02-08 18:21:24', 'created_by' => '124', 'updated_at' => '2018-02-20 18:09:59', 'updated_by' => '124'),
            array('user_id' => '173', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-02-10 15:40:19', 'created_by' => '124', 'updated_at' => '2018-02-24 20:49:09', 'updated_by' => '124'),
            array('user_id' => '174', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-02-10 15:52:42', 'created_by' => '124', 'updated_at' => '2018-02-12 18:40:35', 'updated_by' => '124'),
            array('user_id' => '175', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-02-10 16:40:05', 'created_by' => '124', 'updated_at' => '2018-03-02 23:43:57', 'updated_by' => '124'),
            array('user_id' => '176', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-02-12 13:59:34', 'created_by' => '124', 'updated_at' => NULL, 'updated_by' => NULL),
            array('user_id' => '178', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-02-12 19:43:53', 'created_by' => '85', 'updated_at' => '2018-04-20 22:31:38', 'updated_by' => '85'),
            array('user_id' => '181', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-02-13 10:12:52', 'created_by' => '142', 'updated_at' => '2018-04-16 17:47:31', 'updated_by' => '142'),
            array('user_id' => '182', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-02-13 10:48:51', 'created_by' => '124', 'updated_at' => '2018-03-18 21:44:25', 'updated_by' => '124'),
            array('user_id' => '183', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-02-13 10:58:38', 'created_by' => '124', 'updated_at' => '2018-03-18 21:45:15', 'updated_by' => '124'),
            array('user_id' => '184', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-02-13 13:36:53', 'created_by' => '124', 'updated_at' => '2018-03-05 11:05:55', 'updated_by' => '124'),
            array('user_id' => '185', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-02-15 23:45:15', 'created_by' => '178', 'updated_at' => '2018-02-21 13:02:26', 'updated_by' => '178'),
            array('user_id' => '186', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-02-16 13:02:33', 'created_by' => '178', 'updated_at' => '2018-02-17 11:23:37', 'updated_by' => '178'),
            array('user_id' => '188', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-02-16 16:57:32', 'created_by' => '124', 'updated_at' => '2018-04-25 13:37:53', 'updated_by' => '124'),
            array('user_id' => '190', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-02-21 19:59:40', 'created_by' => '124', 'updated_at' => '2018-02-21 20:00:49', 'updated_by' => '124'),
            array('user_id' => '191', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-02-22 10:28:00', 'created_by' => '124', 'updated_at' => '2018-03-25 17:13:09', 'updated_by' => '124'),
            array('user_id' => '192', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-02-22 10:32:32', 'created_by' => '124', 'updated_at' => '2018-02-27 12:32:55', 'updated_by' => '124'),
            array('user_id' => '193', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-02-22 15:16:11', 'created_by' => '124', 'updated_at' => '2018-02-22 15:23:59', 'updated_by' => '124'),
            array('user_id' => '195', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-02-26 11:56:24', 'created_by' => '142', 'updated_at' => '2018-04-02 12:19:03', 'updated_by' => '142'),
            array('user_id' => '196', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-02-26 16:33:42', 'created_by' => '142', 'updated_at' => '2018-04-10 18:53:11', 'updated_by' => '142'),
            array('user_id' => '197', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-03-02 14:46:40', 'created_by' => '124', 'updated_at' => NULL, 'updated_by' => NULL),
            array('user_id' => '198', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-03-03 21:20:27', 'created_by' => '85', 'updated_at' => '2018-03-31 16:56:38', 'updated_by' => '85'),
            array('user_id' => '199', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-03-05 12:02:25', 'created_by' => '142', 'updated_at' => '2018-04-06 18:12:29', 'updated_by' => '142'),
            array('user_id' => '200', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-03-05 16:10:02', 'created_by' => '142', 'updated_at' => '2018-04-02 13:34:11', 'updated_by' => '142'),
            array('user_id' => '201', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-03-06 15:00:26', 'created_by' => '198', 'updated_at' => '2018-03-06 15:16:14', 'updated_by' => '198'),
            array('user_id' => '202', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-03-06 15:38:52', 'created_by' => '178', 'updated_at' => NULL, 'updated_by' => NULL),
            array('user_id' => '203', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-03-06 18:31:38', 'created_by' => '198', 'updated_at' => '2018-03-06 18:33:21', 'updated_by' => '198'),
            array('user_id' => '204', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-03-10 09:25:43', 'created_by' => '198', 'updated_at' => NULL, 'updated_by' => NULL),
            array('user_id' => '205', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-03-14 16:12:01', 'created_by' => '147', 'updated_at' => '2018-03-14 16:31:22', 'updated_by' => '147'),
            array('user_id' => '207', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-03-23 18:05:38', 'created_by' => '124', 'updated_at' => '2018-03-24 23:46:57', 'updated_by' => '124'),
            array('user_id' => '208', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-03-23 18:30:43', 'created_by' => '85', 'updated_at' => '2018-03-26 22:52:37', 'updated_by' => '85'),
            array('user_id' => '209', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-03-24 10:27:32', 'created_by' => '142', 'updated_at' => '2018-03-27 11:57:53', 'updated_by' => '142'),
            array('user_id' => '210', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3Sjov2ukTEOQvqcyZjzAUsHA37A+4mkPu4aBC9qzyDESQ710fHqLDbsKJUjHqwqEGxAJ6v3Jow7fRfcQ9+0EmROGdbfxgRzMp6rWBNQ/ekrhA=', 'created_at' => '2018-03-24 13:10:29', 'created_by' => '208', 'updated_at' => '2018-04-21 17:23:58', 'updated_by' => '208'),
            array('user_id' => '211', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-03-24 15:02:44', 'created_by' => '124', 'updated_at' => NULL, 'updated_by' => NULL),
            array('user_id' => '212', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3Sjov2ukTEOQvqcyZjzAUsHA37A+4mkPu4aBC9qzyDESQ710fHqLDbsKJUjHqwqEGxAJ6v3Jow7fRfcQ9+0EmROGdbfxgRzMp6rWBNQ/ekrhA=', 'created_at' => '2018-03-24 15:34:49', 'created_by' => '208', 'updated_at' => '2018-03-29 02:34:15', 'updated_by' => '208'),
            array('user_id' => '215', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3Sjov2ukTEOQvqcyZjzAUsHA37A+4mkPu4aBC9qzyDESQ710fHqLDbsKJUjHqwqEGxAJ6v3Jow7fRfcQ9+0EmROGdbfxgRzMp6rWBNQ/ekrhA=', 'created_at' => '2018-03-24 16:12:39', 'created_by' => '208', 'updated_at' => '2018-04-16 00:35:21', 'updated_by' => '208'),
            array('user_id' => '216', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3Sjov2ukTEOQvqcyZjzAUsHA37A+4mkPu4aBC9qzyDESQ710fHqLDbsKJUjHqwqEGxAJ6v3Jow7fRfcQ9+0EmROGdbfxgRzMp6rWBNQ/ekrhA=', 'created_at' => '2018-03-24 16:55:42', 'created_by' => '208', 'updated_at' => '2018-04-21 00:25:59', 'updated_by' => '208'),
            array('user_id' => '217', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3Sjov2ukTEOQvqcyZjzAUsHA37A+4mkPu4aBC9qzyDESQ710fHqLDbsKJUjHqwqEGxAJ6v3Jow7fRfcQ9+0EmROGdbfxgRzMp6rWBNQ/ekrhA=', 'created_at' => '2018-03-24 19:01:17', 'created_by' => '208', 'updated_at' => '2018-04-02 16:23:23', 'updated_by' => '208'),
            array('user_id' => '218', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3Sjov2ukTEOQvqcyZjzAUsHA37A+4mkPu4aBC9qzyDESQ710fHqLDbsKJUjHqwqEGxAJ6v3Jow7fRfcQ9+0EmROGdbfxgRzMp6rWBNQ/ekrhA=', 'created_at' => '2018-03-24 19:28:56', 'created_by' => '208', 'updated_at' => '2018-03-29 02:32:40', 'updated_by' => '208'),
            array('user_id' => '219', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3Sjov2ukTEOQvqcyZjzAUsHA37A+4mkPu4aBC9qzyDESQ710fHqLDbsKJUjHqwqEGxAJ6v3Jow7fRfcQ9+0EmROGdbfxgRzMp6rWBNQ/ekrhA=', 'created_at' => '2018-03-24 19:48:09', 'created_by' => '208', 'updated_at' => '2018-03-31 19:12:39', 'updated_by' => '208'),
            array('user_id' => '220', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3Sjov2ukTEOQvqcyZjzAUsHA37A+4mkPu4aBC9qzyDESQ710fHqLDbsKJUjHqwqEGxAJ6v3Jow7fRfcQ9+0EmROGdbfxgRzMp6rWBNQ/ekrhA=', 'created_at' => '2018-03-25 17:20:33', 'created_by' => '208', 'updated_at' => '2018-04-21 20:44:21', 'updated_by' => '208'),
            array('user_id' => '221', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3Sjov2ukTEOQvqcyZjzAUsHA37A+4mkPu4aBC9qzyDESQ710fHqLDbsKJUjHqwqEGxAJ6v3Jow7fRfcQ9+0EmROGdbfxgRzMp6rWBNQ/ekrhA=', 'created_at' => '2018-03-26 13:15:21', 'created_by' => '208', 'updated_at' => '2018-04-17 10:31:03', 'updated_by' => '208'),
            array('user_id' => '222', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3Sjov2ukTEOQvqcyZjzAUsHA37A+4mkPu4aBC9qzyDESQ710fHqLDbsKJUjHqwqEGxAJ6v3Jow7fRfcQ9+0EmROGdbfxgRzMp6rWBNQ/ekrhA=', 'created_at' => '2018-03-26 15:12:43', 'created_by' => '208', 'updated_at' => '2018-04-24 15:06:07', 'updated_by' => '208'),
            array('user_id' => '223', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-03-27 09:10:14', 'created_by' => '142', 'updated_at' => '2018-03-27 09:28:20', 'updated_by' => '142'),
            array('user_id' => '224', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-03-28 16:58:31', 'created_by' => '85', 'updated_at' => '2018-04-15 14:51:39', 'updated_by' => '85'),
            array('user_id' => '225', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-03-28 17:07:20', 'created_by' => '224', 'updated_at' => NULL, 'updated_by' => NULL),
            array('user_id' => '226', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-03-29 09:53:38', 'created_by' => '85', 'updated_at' => '2018-04-23 11:44:13', 'updated_by' => '85'),
            array('user_id' => '227', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-03-29 11:51:58', 'created_by' => '124', 'updated_at' => NULL, 'updated_by' => NULL),
            array('user_id' => '228', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-03-29 12:32:44', 'created_by' => '226', 'updated_at' => '2018-03-29 12:36:59', 'updated_by' => '226'),
            array('user_id' => '229', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-03-29 12:36:07', 'created_by' => '226', 'updated_at' => '2018-04-19 11:24:34', 'updated_by' => '226'),
            array('user_id' => '230', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-03-29 12:40:28', 'created_by' => '124', 'updated_at' => '2018-04-24 20:48:31', 'updated_by' => '124'),
            array('user_id' => '231', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-03-29 14:06:43', 'created_by' => '224', 'updated_at' => '2018-04-20 21:40:56', 'updated_by' => '224'),
            array('user_id' => '232', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3Sjov2ukTEOQvqcyZjzAUsHA37A+4mkPu4aBC9qzyDESQ710fHqLDbsKJUjHqwqEGxAJ6v3Jow7fRfcQ9+0EmROGdbfxgRzMp6rWBNQ/ekrhA=', 'created_at' => '2018-03-29 15:42:01', 'created_by' => '208', 'updated_at' => NULL, 'updated_by' => NULL),
            array('user_id' => '233', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-03-29 16:12:20', 'created_by' => '224', 'updated_at' => NULL, 'updated_by' => NULL),
            array('user_id' => '234', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3Sjov2ukTEOQvqcyZjzAUsHA37A+4mkPu4aBC9qzyDESQ710fHqLDbsKJUjHqwqEGxAJ6v3Jow7fRfcQ9+0EmROGdbfxgRzMp6rWBNQ/ekrhA=', 'created_at' => '2018-03-29 19:10:34', 'created_by' => '208', 'updated_at' => '2018-04-25 01:52:59', 'updated_by' => '208'),
            array('user_id' => '235', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-03-30 09:09:20', 'created_by' => '226', 'updated_at' => NULL, 'updated_by' => NULL),
            array('user_id' => '236', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-03-30 09:14:55', 'created_by' => '226', 'updated_at' => '2018-04-19 11:31:29', 'updated_by' => '226'),
            array('user_id' => '237', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-03-30 09:19:15', 'created_by' => '226', 'updated_at' => '2018-04-23 11:48:22', 'updated_by' => '226'),
            array('user_id' => '238', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-03-30 09:23:10', 'created_by' => '226', 'updated_at' => '2018-04-06 14:58:15', 'updated_by' => '226'),
            array('user_id' => '239', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-03-30 09:28:12', 'created_by' => '226', 'updated_at' => '2018-04-23 11:46:05', 'updated_by' => '226'),
            array('user_id' => '240', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-03-30 09:38:32', 'created_by' => '226', 'updated_at' => '2018-04-23 11:47:26', 'updated_by' => '226'),
            array('user_id' => '241', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-03-30 09:42:41', 'created_by' => '226', 'updated_at' => '2018-04-04 15:52:20', 'updated_by' => '226'),
            array('user_id' => '242', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3Sjov2ukTEOQvqcyZjzAUsHA37A+4mkPu4aBC9qzyDESQ710fHqLDbsKJUjHqwqEGxAJ6v3Jow7fRfcQ9+0EmROGdbfxgRzMp6rWBNQ/ekrhA=', 'created_at' => '2018-03-30 13:29:10', 'created_by' => '208', 'updated_at' => '2018-04-23 12:21:20', 'updated_by' => '208'),
            array('user_id' => '243', 'access_data' => 'nW1KfTpeCgsmTzvNnzWw2CS7Zo4jHR41JptJoaUdk1DsocjG4jg0fqgkfeSMW/167xKL6JPURWtsuPrRYnt6xKbRbMM0NMxK8An9NH34/WDhmGxB/a3SfhUieLLOEn+RnfYxeEmkgckqoUugKHpaoksJWu/8MrqdyOl8U1XpmzM=', 'created_at' => '2018-03-30 16:49:12', 'created_by' => '208', 'updated_at' => NULL, 'updated_by' => NULL),
            array('user_id' => '244', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3Sjov2ukTEOQvqcyZjzAUsHA37A+4mkPu4aBC9qzyDESQ710fHqLDbsKJUjHqwqEGxAJ6v3Jow7fRfcQ9+0EmROGdbfxgRzMp6rWBNQ/ekrhA=', 'created_at' => '2018-03-30 20:26:05', 'created_by' => '208', 'updated_at' => NULL, 'updated_by' => NULL),
            array('user_id' => '245', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3Sjov2ukTEOQvqcyZjzAUsHA37A+4mkPu4aBC9qzyDESQ710fHqLDbsKJUjHqwqEGxAJ6v3Jow7fRfcQ9+0EmROGdbfxgRzMp6rWBNQ/ekrhA=', 'created_at' => '2018-03-30 20:41:38', 'created_by' => '208', 'updated_at' => '2018-03-31 19:40:12', 'updated_by' => '208'),
            array('user_id' => '246', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-03-30 21:29:09', 'created_by' => '226', 'updated_at' => '2018-04-18 12:35:16', 'updated_by' => '226'),
            array('user_id' => '247', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-03-31 13:43:02', 'created_by' => '142', 'updated_at' => '2018-04-11 15:17:08', 'updated_by' => '142'),
            array('user_id' => '249', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3Sjov2ukTEOQvqcyZjzAUsHA37A+4mkPu4aBC9qzyDESQ710fHqLDbsKJUjHqwqEGxAJ6v3Jow7fRfcQ9+0EmROGdbfxgRzMp6rWBNQ/ekrhA=', 'created_at' => '2018-04-04 12:27:54', 'created_by' => '208', 'updated_at' => '2018-04-23 19:19:11', 'updated_by' => '249'),
            array('user_id' => '250', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3Sjov2ukTEOQvqcyZjzAUsHA37A+4mkPu4aBC9qzyDESQ710fHqLDbsKJUjHqwqEGxAJ6v3Jow7fRfcQ9+0EmROGdbfxgRzMp6rWBNQ/ekrhA=', 'created_at' => '2018-04-04 12:57:59', 'created_by' => '208', 'updated_at' => '2018-04-04 13:08:43', 'updated_by' => '208'),
            array('user_id' => '251', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3Sjov2ukTEOQvqcyZjzAUsHA37A+4mkPu4aBC9qzyDESQ710fHqLDbsKJUjHqwqEGxAJ6v3Jow7fRfcQ9+0EmROGdbfxgRzMp6rWBNQ/ekrhA=', 'created_at' => '2018-04-04 15:15:01', 'created_by' => '208', 'updated_at' => '2018-04-04 15:18:12', 'updated_by' => '208'),
            array('user_id' => '252', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3Sjov2ukTEOQvqcyZjzAUsHA37A+4mkPu4aBC9qzyDESQ710fHqLDbsKJUjHqwqEGxAJ6v3Jow7fRfcQ9+0EmROGdbfxgRzMp6rWBNQ/ekrhA=', 'created_at' => '2018-04-05 12:14:04', 'created_by' => '208', 'updated_at' => '2018-04-20 17:50:34', 'updated_by' => '208'),
            array('user_id' => '253', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3Sjov2ukTEOQvqcyZjzAUsHA37A+4mkPu4aBC9qzyDESQ710fHqLDbsKJUjHqwqEGxAJ6v3Jow7fRfcQ9+0EmROGdbfxgRzMp6rWBNQ/ekrhA=', 'created_at' => '2018-04-05 19:00:37', 'created_by' => '208', 'updated_at' => NULL, 'updated_by' => NULL),
            array('user_id' => '254', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3Sjov2ukTEOQvqcyZjzAUsHA37A+4mkPu4aBC9qzyDESQ710fHqLDbsKJUjHqwqEGxAJ6v3Jow7fRfcQ9+0EmROGdbfxgRzMp6rWBNQ/ekrhA=', 'created_at' => '2018-04-06 16:29:13', 'created_by' => '208', 'updated_at' => '2018-04-23 11:47:49', 'updated_by' => '208'),
            array('user_id' => '255', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3Sjov2ukTEOQvqcyZjzAUsHA37A+4mkPu4aBC9qzyDESQ710fHqLDbsKJUjHqwqEGxAJ6v3Jow7fRfcQ9+0EmROGdbfxgRzMp6rWBNQ/ekrhA=', 'created_at' => '2018-04-09 14:12:43', 'created_by' => '208', 'updated_at' => '2018-04-09 14:20:28', 'updated_by' => '208'),
            array('user_id' => '256', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-04-15 13:50:20', 'created_by' => '85', 'updated_at' => '2018-04-20 22:29:52', 'updated_by' => '85'),
            array('user_id' => '257', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3Sjov2ukTEOQvqcyZjzAUsHA37A+4mkPu4aBC9qzyDESQ710fHqLDbsKJUjHqwqEGxAJ6v3Jow7fRfcQ9+0EmROGdbfxgRzMp6rWBNQ/ekrhA=', 'created_at' => '2018-04-17 11:32:01', 'created_by' => '208', 'updated_at' => NULL, 'updated_by' => NULL),
            array('user_id' => '258', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3SrTbpfWvMHfdcGCy2hz98KBKH5ZR+EPrH0N7OuSPYXrsZNtrLwcn0BLerRRpR7deEp7SUfW28f+uNHnq04XmAlir2djDTIpWy+Vmmq3H3X5Y=', 'created_at' => '2018-04-17 13:13:07', 'created_by' => '226', 'updated_at' => '2018-04-18 13:48:46', 'updated_by' => '226'),
            array('user_id' => '259', 'access_data' => 'YEUO6HL3JEj6qxCnuHSgQ4B4PCRn5ng4wbp80Mzcq1pnnP++yQnlDa36zVvg5U3Sjov2ukTEOQvqcyZjzAUsHA37A+4mkPu4aBC9qzyDESQ710fHqLDbsKJUjHqwqEGxAJ6v3Jow7fRfcQ9+0EmROGdbfxgRzMp6rWBNQ/ekrhA=', 'created_at' => '2018-04-24 11:06:22', 'created_by' => '208', 'updated_at' => NULL, 'updated_by' => NULL)
        );
//        try{
        foreach ($tb_users_access as $row) {
//            dd($row);
            if (User::find($row['user_id'])) {
                $iv = "1234567899421507";
                $decrypt = openssl_decrypt($row['access_data'], 'aes128', 'IfUCaN_0$$', 0, $iv);
                $access_data = json_decode($decrypt, true);
//
                foreach (Service::all() as $service) {
//                    dd($access_data[str_slug($service->name,'-')]);
                    $status = 0;
                    if ($service->id == 3) {
                        $service->name = str_replace('/', '', $service->name);
                    }
                    if (isset($access_data[str_slug($service->name, '-')]) && $access_data[str_slug($service->name, '-')] == 1) {
                        $status = 1;
                    }
                    UserAccess::insert([
                        'user_id' => $row['user_id'],
                        'service_id' => $service->id,
                        'status' => $status,
                        'created_at' => $row['created_at'],
                        'created_by' => $row['created_by'],
                        'updated_at' => $row['updated_at'],
                        'updated_by' => $row['updated_by']
                    ]);
                }
            }
        }
//        }catch (\Exception $e){
//            echo "Exception throws => ".$e->getMessage();
//        }
        return true;

    }

    static function migrate_service_config()
    {
        $tb_service_config = array(
            array('id' => '1', 'service_id' => '1', 'config' => '{"access_data":["188","131","37","53"]}', 'type' => 'default', 'tel_prefix' => NULL, 'count_prefix' => NULL, 'country_id' => NULL, 'created_at' => '2017-08-21 05:37:04', 'created_by' => '10', 'updated_at' => '2017-08-22 12:58:24', 'updated_by' => '10'),
            array('id' => '2', 'service_id' => '2', 'config' => '{"users":["53","47"]}', 'type' => 'default', 'tel_prefix' => '2217', 'count_prefix' => '4', 'country_id' => '188', 'created_at' => '2017-08-21 12:34:50', 'created_by' => '10', 'updated_at' => '2017-09-03 11:16:54', 'updated_by' => '53'),
            array('id' => '3', 'service_id' => '2', 'config' => '{"users":["53","47"]}', 'type' => 'default', 'tel_prefix' => '2236,2237,2238,2239', 'count_prefix' => '4', 'country_id' => '131', 'created_at' => '2017-08-21 12:34:50', 'created_by' => '10', 'updated_at' => '2017-09-03 11:16:54', 'updated_by' => '53'),
            array('id' => '4', 'service_id' => '2', 'config' => '{"users":[""]}', 'type' => 'default', 'tel_prefix' => '', 'count_prefix' => NULL, 'country_id' => '37', 'created_at' => '2017-08-21 12:34:50', 'created_by' => '10', 'updated_at' => '2017-09-03 11:16:54', 'updated_by' => '53'),
            array('id' => '5', 'service_id' => '2', 'config' => '{"users":[""]}', 'type' => 'default', 'tel_prefix' => '', 'count_prefix' => NULL, 'country_id' => '53', 'created_at' => '2017-08-21 12:34:50', 'created_by' => '10', 'updated_at' => '2017-09-03 11:16:54', 'updated_by' => '53'),
            array('id' => '6', 'service_id' => '2', 'config' => '{"users":[""]}', 'type' => 'default', 'tel_prefix' => '', 'count_prefix' => NULL, 'country_id' => '78', 'created_at' => '2017-08-21 12:34:50', 'created_by' => '10', 'updated_at' => '2017-09-03 11:16:54', 'updated_by' => '53'),
            array('id' => '7', 'service_id' => '2', 'config' => '{"users":[""]}', 'type' => 'default', 'tel_prefix' => '', 'count_prefix' => NULL, 'country_id' => '213', 'created_at' => '2017-08-21 12:34:50', 'created_by' => '10', 'updated_at' => '2017-09-03 11:16:54', 'updated_by' => '53'),
            array('id' => '8', 'service_id' => '2', 'config' => '{"users":[""]}', 'type' => 'default', 'tel_prefix' => '', 'count_prefix' => NULL, 'country_id' => '90', 'created_at' => '2017-08-21 12:34:50', 'created_by' => '10', 'updated_at' => '2017-09-03 11:16:54', 'updated_by' => '53')
        );
        ServiceConfig::insert($tb_service_config);
        return true;
    }

    static function migrate_credit_limits()
    {
        $tb_commissions = array(
            array('id' => '12', 'type' => 'credit', 'user_id' => '54', 'credit_limit' => '-1000', 'created_at' => '2017-05-05 07:46:05', 'created_by' => '1', 'updated_at' => '2017-11-07 14:03:09', 'updated_by' => '1'),
            array('id' => '13', 'type' => 'credit', 'user_id' => '1', 'credit_limit' => '-500', 'created_at' => '2017-05-11 13:07:28', 'created_by' => '1', 'updated_at' => '2017-07-02 09:27:45', 'updated_by' => '1'),
            array('id' => '14', 'type' => 'credit', 'user_id' => '53', 'credit_limit' => '-50', 'created_at' => '2017-05-17 13:50:06', 'created_by' => '1', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '15', 'type' => 'custom', 'user_id' => '60', 'credit_limit' => '-100', 'created_at' => '2017-05-29 10:00:32', 'created_by' => '53', 'updated_at' => '2017-07-31 12:56:49', 'updated_by' => '53'),
            array('id' => '18', 'type' => 'credit', 'user_id' => '47', 'credit_limit' => '-1000', 'created_at' => '2017-07-31 13:29:46', 'created_by' => '53', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '19', 'type' => 'credit', 'user_id' => '66', 'credit_limit' => '-700', 'created_at' => '2017-07-31 16:23:35', 'created_by' => '53', 'updated_at' => '2018-01-23 18:15:10', 'updated_by' => '46'),
            array('id' => '20', 'type' => 'credit', 'user_id' => '46', 'credit_limit' => '-2000', 'created_at' => '2017-07-31 23:15:15', 'created_by' => '46', 'updated_at' => '2018-01-08 18:31:03', 'updated_by' => '46'),
            array('id' => '52', 'type' => 'credit', 'user_id' => '68', 'credit_limit' => '-200', 'created_at' => '2017-09-11 18:13:56', 'created_by' => '1', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '64', 'type' => 'credit', 'user_id' => '70', 'credit_limit' => '-100', 'created_at' => '2017-10-25 07:12:34', 'created_by' => '1', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '77', 'type' => 'credit', 'user_id' => '72', 'credit_limit' => '-1', 'created_at' => '2017-10-25 07:59:11', 'created_by' => '70', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '96', 'type' => 'credit', 'user_id' => '75', 'credit_limit' => '-100', 'created_at' => '2017-10-27 13:53:29', 'created_by' => '53', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '103', 'type' => 'credit', 'user_id' => '76', 'credit_limit' => '-100', 'created_at' => '2017-10-27 13:55:46', 'created_by' => '75', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '110', 'type' => 'credit', 'user_id' => '77', 'credit_limit' => '-30', 'created_at' => '2017-10-27 18:33:56', 'created_by' => '53', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '123', 'type' => 'credit', 'user_id' => '78', 'credit_limit' => '-100', 'created_at' => '2017-11-01 14:50:03', 'created_by' => '70', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '124', 'type' => 'credit', 'user_id' => '79', 'credit_limit' => '-100', 'created_at' => '2017-11-02 06:46:45', 'created_by' => '53', 'updated_at' => '2017-11-02 07:53:22', 'updated_by' => '53'),
            array('id' => '139', 'type' => 'credit', 'user_id' => '80', 'credit_limit' => '-10', 'created_at' => '2017-11-02 07:14:13', 'created_by' => '79', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '140', 'type' => 'credit', 'user_id' => '81', 'credit_limit' => '-50', 'created_at' => '2017-11-02 15:33:27', 'created_by' => '79', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '147', 'type' => 'credit', 'user_id' => '82', 'credit_limit' => '-50', 'created_at' => '2017-11-06 07:25:43', 'created_by' => '53', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '153', 'type' => 'credit', 'user_id' => '83', 'credit_limit' => '-50', 'created_at' => '2017-11-06 07:35:06', 'created_by' => '82', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '159', 'type' => 'credit', 'user_id' => '84', 'credit_limit' => '-50', 'created_at' => '2017-11-06 07:36:43', 'created_by' => '82', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '165', 'type' => 'credit', 'user_id' => '85', 'credit_limit' => '-600', 'created_at' => '2017-11-07 08:47:09', 'created_by' => '1', 'updated_at' => '2018-03-28 18:18:47', 'updated_by' => '46'),
            array('id' => '171', 'type' => 'credit', 'user_id' => '86', 'credit_limit' => '-50', 'created_at' => '2017-11-08 03:23:43', 'created_by' => '85', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '177', 'type' => 'credit', 'user_id' => '87', 'credit_limit' => '-50', 'created_at' => '2017-11-08 03:31:36', 'created_by' => '85', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '183', 'type' => 'credit', 'user_id' => '88', 'credit_limit' => '-50', 'created_at' => '2017-11-08 03:34:31', 'created_by' => '87', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '189', 'type' => 'credit', 'user_id' => '89', 'credit_limit' => '-50', 'created_at' => '2017-11-13 16:28:12', 'created_by' => '85', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '195', 'type' => 'credit', 'user_id' => '90', 'credit_limit' => '-1000', 'created_at' => '2017-11-15 18:36:34', 'created_by' => '53', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '203', 'type' => 'credit', 'user_id' => '92', 'credit_limit' => '-50', 'created_at' => '2017-11-24 23:11:30', 'created_by' => '87', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '209', 'type' => 'credit', 'user_id' => '93', 'credit_limit' => '-50', 'created_at' => '2017-11-25 13:44:09', 'created_by' => '85', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '215', 'type' => 'credit', 'user_id' => '95', 'credit_limit' => '-100', 'created_at' => '2017-11-27 09:21:51', 'created_by' => '53', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '221', 'type' => 'credit', 'user_id' => '102', 'credit_limit' => '-50', 'created_at' => '2017-11-27 17:26:46', 'created_by' => '53', 'updated_at' => '2018-03-07 17:54:51', 'updated_by' => '46'),
            array('id' => '236', 'type' => 'credit', 'user_id' => '105', 'credit_limit' => '-80', 'created_at' => '2017-11-29 13:49:43', 'created_by' => '102', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '242', 'type' => 'credit', 'user_id' => '106', 'credit_limit' => '-10', 'created_at' => '2017-11-29 13:51:25', 'created_by' => '102', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '258', 'type' => 'credit', 'user_id' => '109', 'credit_limit' => '-10', 'created_at' => '2017-12-04 07:17:03', 'created_by' => '70', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '264', 'type' => 'credit', 'user_id' => '110', 'credit_limit' => '-50', 'created_at' => '2017-12-06 15:02:43', 'created_by' => '85', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '270', 'type' => 'credit', 'user_id' => '111', 'credit_limit' => '-50', 'created_at' => '2017-12-06 15:27:55', 'created_by' => '110', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '281', 'type' => 'credit', 'user_id' => '104', 'credit_limit' => '-100', 'created_at' => '2017-12-07 17:51:35', 'created_by' => '103', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '282', 'type' => 'credit', 'user_id' => '103', 'credit_limit' => '-100', 'created_at' => '2017-12-08 11:57:21', 'created_by' => '85', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '298', 'type' => 'credit', 'user_id' => '116', 'credit_limit' => '-100', 'created_at' => '2017-12-08 21:25:12', 'created_by' => '46', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '304', 'type' => 'credit', 'user_id' => '117', 'credit_limit' => '-1000', 'created_at' => '2017-12-09 00:39:06', 'created_by' => '85', 'updated_at' => '2018-01-22 23:46:59', 'updated_by' => '85'),
            array('id' => '315', 'type' => 'credit', 'user_id' => '119', 'credit_limit' => '-20', 'created_at' => '2017-12-10 16:34:04', 'created_by' => '85', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '321', 'type' => 'credit', 'user_id' => '120', 'credit_limit' => '-10', 'created_at' => '2017-12-13 08:35:26', 'created_by' => '53', 'updated_at' => '2017-12-13 08:40:49', 'updated_by' => '53'),
            array('id' => '327', 'type' => 'credit', 'user_id' => '121', 'credit_limit' => '-10', 'created_at' => '2017-12-13 08:39:57', 'created_by' => '53', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '333', 'type' => 'credit', 'user_id' => '122', 'credit_limit' => '-10', 'created_at' => '2017-12-13 08:45:06', 'created_by' => '53', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '346', 'type' => 'credit', 'user_id' => '138', 'credit_limit' => '-200', 'created_at' => '2018-01-23 05:19:53', 'created_by' => '85', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '379', 'type' => 'credit', 'user_id' => '126', 'credit_limit' => '-1', 'created_at' => '2018-01-24 11:01:52', 'created_by' => '124', 'updated_at' => '2018-01-24 22:41:56', 'updated_by' => '124'),
            array('id' => '380', 'type' => 'credit', 'user_id' => '141', 'credit_limit' => '-1', 'created_at' => '2018-01-24 22:33:36', 'created_by' => '85', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '426', 'type' => 'credit', 'user_id' => '146', 'credit_limit' => '-200', 'created_at' => '2018-01-28 15:15:23', 'created_by' => '142', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '432', 'type' => 'credit', 'user_id' => '142', 'credit_limit' => '-1000', 'created_at' => '2018-01-28 15:29:46', 'created_by' => '85', 'updated_at' => '2018-03-22 16:44:45', 'updated_by' => '85'),
            array('id' => '483', 'type' => 'credit', 'user_id' => '157', 'credit_limit' => '-300', 'created_at' => '2018-01-28 23:13:52', 'created_by' => '147', 'updated_at' => '2018-03-15 22:28:20', 'updated_by' => '147'),
            array('id' => '489', 'type' => 'credit', 'user_id' => '158', 'credit_limit' => '-46', 'created_at' => '2018-01-29 06:50:31', 'created_by' => '85', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '490', 'type' => 'credit', 'user_id' => '159', 'credit_limit' => '-50', 'created_at' => '2018-01-29 06:53:05', 'created_by' => '85', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '491', 'type' => 'credit', 'user_id' => '131', 'credit_limit' => '-300', 'created_at' => '2018-01-29 19:09:13', 'created_by' => '124', 'updated_at' => '2018-03-05 10:24:55', 'updated_by' => '124'),
            array('id' => '502', 'type' => 'credit', 'user_id' => '163', 'credit_limit' => '-50', 'created_at' => '2018-02-02 11:06:31', 'created_by' => '124', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '513', 'type' => 'credit', 'user_id' => '165', 'credit_limit' => '-200', 'created_at' => '2018-02-03 13:07:19', 'created_by' => '124', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '514', 'type' => 'credit', 'user_id' => '166', 'credit_limit' => '-700', 'created_at' => '2018-02-06 12:46:29', 'created_by' => '142', 'updated_at' => '2018-04-02 13:35:24', 'updated_by' => '142'),
            array('id' => '520', 'type' => 'credit', 'user_id' => '140', 'credit_limit' => '-300', 'created_at' => '2018-02-06 18:10:50', 'created_by' => '124', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '521', 'type' => 'credit', 'user_id' => '143', 'credit_limit' => '-500', 'created_at' => '2018-02-06 20:59:48', 'created_by' => '124', 'updated_at' => '2018-02-17 00:02:38', 'updated_by' => '124'),
            array('id' => '527', 'type' => 'credit', 'user_id' => '170', 'credit_limit' => '-600', 'created_at' => '2018-02-07 12:12:11', 'created_by' => '142', 'updated_at' => '2018-03-30 19:48:26', 'updated_by' => '142'),
            array('id' => '533', 'type' => 'credit', 'user_id' => '171', 'credit_limit' => '-100', 'created_at' => '2018-02-08 16:16:48', 'created_by' => '169', 'updated_at' => '2018-02-28 17:00:15', 'updated_by' => '169'),
            array('id' => '539', 'type' => 'credit', 'user_id' => '172', 'credit_limit' => '-1', 'created_at' => '2018-02-08 18:28:45', 'created_by' => '124', 'updated_at' => '2018-02-11 16:18:27', 'updated_by' => '124'),
            array('id' => '540', 'type' => 'credit', 'user_id' => '169', 'credit_limit' => '-300', 'created_at' => '2018-02-08 19:14:33', 'created_by' => '85', 'updated_at' => '2018-03-29 13:50:36', 'updated_by' => '85'),
            array('id' => '566', 'type' => 'credit', 'user_id' => '124', 'credit_limit' => '-2000', 'created_at' => '2018-02-11 13:28:01', 'created_by' => '85', 'updated_at' => '2018-02-20 14:55:16', 'updated_by' => '85'),
            array('id' => '567', 'type' => 'credit', 'user_id' => '147', 'credit_limit' => '-1000', 'created_at' => '2018-02-11 13:29:00', 'created_by' => '85', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '568', 'type' => 'credit', 'user_id' => '176', 'credit_limit' => '-300', 'created_at' => '2018-02-12 13:59:34', 'created_by' => '124', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '579', 'type' => 'credit', 'user_id' => '177', 'credit_limit' => '-1', 'created_at' => '2018-02-12 17:16:33', 'created_by' => '134', 'updated_at' => '2018-02-12 18:51:23', 'updated_by' => '134'),
            array('id' => '595', 'type' => 'credit', 'user_id' => '181', 'credit_limit' => '-200', 'created_at' => '2018-02-13 10:12:52', 'created_by' => '142', 'updated_at' => '2018-03-19 16:29:41', 'updated_by' => '142'),
            array('id' => '616', 'type' => 'credit', 'user_id' => '175', 'credit_limit' => '-1', 'created_at' => '2018-02-13 16:44:37', 'created_by' => '124', 'updated_at' => '2018-03-02 23:43:57', 'updated_by' => '124'),
            array('id' => '617', 'type' => 'credit', 'user_id' => '134', 'credit_limit' => '-1000', 'created_at' => '2018-02-14 12:47:39', 'created_by' => '85', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '618', 'type' => 'credit', 'user_id' => '125', 'credit_limit' => '-100', 'created_at' => '2018-02-15 13:34:02', 'created_by' => '124', 'updated_at' => '2018-03-05 10:25:31', 'updated_by' => '124'),
            array('id' => '639', 'type' => 'credit', 'user_id' => '183', 'credit_limit' => '-1', 'created_at' => '2018-02-18 16:50:48', 'created_by' => '124', 'updated_at' => '2018-03-02 23:45:10', 'updated_by' => '124'),
            array('id' => '640', 'type' => 'credit', 'user_id' => '189', 'credit_limit' => '-1', 'created_at' => '2018-02-19 18:50:28', 'created_by' => '142', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '651', 'type' => 'credit', 'user_id' => '190', 'credit_limit' => '-50', 'created_at' => '2018-02-21 20:00:49', 'created_by' => '124', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '682', 'type' => 'credit', 'user_id' => '196', 'credit_limit' => '-300', 'created_at' => '2018-02-26 16:38:33', 'created_by' => '142', 'updated_at' => '2018-03-31 18:53:46', 'updated_by' => '142'),
            array('id' => '683', 'type' => 'credit', 'user_id' => '162', 'credit_limit' => '-300', 'created_at' => '2018-02-27 20:39:19', 'created_by' => '142', 'updated_at' => '2018-04-02 18:33:32', 'updated_by' => '142'),
            array('id' => '684', 'type' => 'credit', 'user_id' => '197', 'credit_limit' => '-50', 'created_at' => '2018-03-02 14:46:39', 'created_by' => '124', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '690', 'type' => 'credit', 'user_id' => '198', 'credit_limit' => '-500', 'created_at' => '2018-03-03 21:20:27', 'created_by' => '85', 'updated_at' => '2018-03-31 16:56:38', 'updated_by' => '85'),
            array('id' => '696', 'type' => 'credit', 'user_id' => '199', 'credit_limit' => '-1000', 'created_at' => '2018-03-05 12:02:25', 'created_by' => '142', 'updated_at' => '2018-04-02 11:04:47', 'updated_by' => '142'),
            array('id' => '702', 'type' => 'credit', 'user_id' => '200', 'credit_limit' => '-100', 'created_at' => '2018-03-05 16:10:01', 'created_by' => '142', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '708', 'type' => 'credit', 'user_id' => '201', 'credit_limit' => '-5', 'created_at' => '2018-03-06 15:00:26', 'created_by' => '198', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '714', 'type' => 'credit', 'user_id' => '203', 'credit_limit' => '-10', 'created_at' => '2018-03-06 18:31:38', 'created_by' => '198', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '720', 'type' => 'credit', 'user_id' => '195', 'credit_limit' => '-50', 'created_at' => '2018-03-09 11:03:49', 'created_by' => '142', 'updated_at' => '2018-04-02 12:19:02', 'updated_by' => '142'),
            array('id' => '726', 'type' => 'credit', 'user_id' => '206', 'credit_limit' => '-100', 'created_at' => '2018-03-16 22:56:55', 'created_by' => '67', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '728', 'type' => 'credit', 'user_id' => '207', 'credit_limit' => '-1', 'created_at' => '2018-03-23 18:05:38', 'created_by' => '124', 'updated_at' => '2018-03-24 23:46:57', 'updated_by' => '124'),
            array('id' => '734', 'type' => 'credit', 'user_id' => '208', 'credit_limit' => '-50', 'created_at' => '2018-03-23 18:30:42', 'created_by' => '85', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '745', 'type' => 'credit', 'user_id' => '161', 'credit_limit' => '-100', 'created_at' => '2018-03-25 21:29:25', 'created_by' => '142', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '746', 'type' => 'credit', 'user_id' => '223', 'credit_limit' => '-50', 'created_at' => '2018-03-27 09:10:13', 'created_by' => '142', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '752', 'type' => 'credit', 'user_id' => '224', 'credit_limit' => '-500', 'created_at' => '2018-03-28 16:58:30', 'created_by' => '85', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '758', 'type' => 'credit', 'user_id' => '225', 'credit_limit' => '-100', 'created_at' => '2018-03-28 17:07:19', 'created_by' => '224', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '809', 'type' => 'credit', 'user_id' => '226', 'credit_limit' => '-2000', 'created_at' => '2018-03-29 09:53:38', 'created_by' => '85', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '815', 'type' => 'credit', 'user_id' => '227', 'credit_limit' => '-50', 'created_at' => '2018-03-29 11:51:58', 'created_by' => '124', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '821', 'type' => 'credit', 'user_id' => '228', 'credit_limit' => '-500', 'created_at' => '2018-03-29 12:32:44', 'created_by' => '226', 'updated_at' => '2018-03-29 12:36:59', 'updated_by' => '226'),
            array('id' => '827', 'type' => 'credit', 'user_id' => '229', 'credit_limit' => '-500', 'created_at' => '2018-03-29 12:36:07', 'created_by' => '226', 'updated_at' => '2018-03-29 12:37:26', 'updated_by' => '226'),
            array('id' => '833', 'type' => 'credit', 'user_id' => '230', 'credit_limit' => '-50', 'created_at' => '2018-03-29 12:40:27', 'created_by' => '124', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '844', 'type' => 'credit', 'user_id' => '231', 'credit_limit' => '-50', 'created_at' => '2018-03-29 14:06:43', 'created_by' => '224', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '855', 'type' => 'credit', 'user_id' => '233', 'credit_limit' => '-50', 'created_at' => '2018-03-29 16:12:20', 'created_by' => '224', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '866', 'type' => 'credit', 'user_id' => '235', 'credit_limit' => '-50', 'created_at' => '2018-03-30 09:09:20', 'created_by' => '226', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '872', 'type' => 'credit', 'user_id' => '236', 'credit_limit' => '-50', 'created_at' => '2018-03-30 09:14:55', 'created_by' => '226', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '878', 'type' => 'credit', 'user_id' => '237', 'credit_limit' => '-50', 'created_at' => '2018-03-30 09:19:14', 'created_by' => '226', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '884', 'type' => 'credit', 'user_id' => '238', 'credit_limit' => '-50', 'created_at' => '2018-03-30 09:23:10', 'created_by' => '226', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '890', 'type' => 'credit', 'user_id' => '239', 'credit_limit' => '-50', 'created_at' => '2018-03-30 09:28:12', 'created_by' => '226', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '896', 'type' => 'credit', 'user_id' => '240', 'credit_limit' => '-150', 'created_at' => '2018-03-30 09:38:32', 'created_by' => '226', 'updated_at' => '2018-03-31 08:08:46', 'updated_by' => '226'),
            array('id' => '902', 'type' => 'credit', 'user_id' => '241', 'credit_limit' => '-50', 'created_at' => '2018-03-30 09:42:41', 'created_by' => '226', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '928', 'type' => 'credit', 'user_id' => '246', 'credit_limit' => '-50', 'created_at' => '2018-03-30 21:29:09', 'created_by' => '226', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '934', 'type' => 'credit', 'user_id' => '247', 'credit_limit' => '-200', 'created_at' => '2018-03-31 13:43:02', 'created_by' => '142', 'updated_at' => '2018-03-31 17:22:50', 'updated_by' => '142')
        );
        foreach ($tb_commissions as $row) {
            if (User::find($row['user_id'])) {
                CreditLimit::insert($row);
            }
        }
        return true;
    }

    static function migrate_app_commissions()
    {
        $tb_own_commission = array(
            array('id' => '1', 'service_id' => '1', 'prev_com' => '30', 'commission' => '20', 'created_at' => '2017-11-22 08:47:38', 'created_by' => '1', 'updated_at' => '2017-12-04 08:59:18', 'updated_by' => '1'),
            array('id' => '2', 'service_id' => '2', 'prev_com' => '20', 'commission' => '17', 'created_at' => '2017-12-04 09:05:29', 'created_by' => '1', 'updated_at' => '2018-03-02 11:46:52', 'updated_by' => '53'),
            array('id' => '3', 'service_id' => '4', 'prev_com' => '0', 'commission' => '20', 'created_at' => '2017-12-04 09:06:01', 'created_by' => '1', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '4', 'service_id' => '5', 'prev_com' => '0', 'commission' => '15', 'created_at' => '2017-12-04 09:06:09', 'created_by' => '1', 'updated_at' => NULL, 'updated_by' => NULL),
            array('id' => '5', 'service_id' => '6', 'prev_com' => '0', 'commission' => '18', 'created_at' => '2017-12-04 09:06:17', 'created_by' => '1', 'updated_at' => NULL, 'updated_by' => NULL)
        );
        AppCommission::insert($tb_own_commission);
        return true;
    }

    static function migrate_payments()
    {
        $transactions = array(
            array('transaction_id' => '93', 'user_id' => '26', 'date' => '2017-07-07 08:18:00', 'amount' => '-21.00', 'description' => 'credit balance updated by admin', 'received_by' => '1'),
            array('transaction_id' => '94', 'user_id' => '45', 'date' => '2017-07-07 08:20:00', 'amount' => '40.80', 'description' => 'credit Balance updated by admin', 'received_by' => '1'),
            array('transaction_id' => '95', 'user_id' => '44', 'date' => '2017-07-07 08:25:00', 'amount' => '2.00', 'description' => 'credit Balance updated by admin', 'received_by' => '1'),
            array('transaction_id' => '96', 'user_id' => '39', 'date' => '2017-07-07 08:26:00', 'amount' => '2.00', 'description' => 'credit Balance updated by admin', 'received_by' => '1'),
            array('transaction_id' => '97', 'user_id' => '24', 'date' => '2017-07-07 08:40:00', 'amount' => '0.04', 'description' => 'credit Balance updated by admin', 'received_by' => '1'),
            array('transaction_id' => '98', 'user_id' => '48', 'date' => '2017-07-07 08:41:00', 'amount' => '2.00', 'description' => 'credit Balance updated by admin', 'received_by' => '1'),
            array('transaction_id' => '99', 'user_id' => '42', 'date' => '2017-07-07 08:42:00', 'amount' => '2.00', 'description' => 'credit Balance updated by admin', 'received_by' => '1'),
            array('transaction_id' => '100', 'user_id' => '49', 'date' => '2017-07-07 08:43:00', 'amount' => '1.05', 'description' => 'credit Balance updated by admin', 'received_by' => '1'),
            array('transaction_id' => '101', 'user_id' => '27', 'date' => '2017-07-07 08:44:00', 'amount' => '20.20', 'description' => 'credit Balance updated by admin', 'received_by' => '1'),
            array('transaction_id' => '102', 'user_id' => '46', 'date' => '2017-07-07 08:44:00', 'amount' => '32.45', 'description' => 'credit Balance updated by admin', 'received_by' => '1'),
            array('transaction_id' => '103', 'user_id' => '51', 'date' => '2017-07-07 08:45:00', 'amount' => '143.00', 'description' => 'credit Balance updated by admin', 'received_by' => '1'),
            array('transaction_id' => '105', 'user_id' => '22', 'date' => '2017-07-07 08:46:00', 'amount' => '2.39', 'description' => 'credit Balance updated by admin', 'received_by' => '1'),
            array('transaction_id' => '106', 'user_id' => '40', 'date' => '2017-07-07 09:06:00', 'amount' => '-1004.16', 'description' => 'credit Balance updated by admin', 'received_by' => '1'),
            array('transaction_id' => '107', 'user_id' => '35', 'date' => '2017-07-07 09:07:00', 'amount' => '1.00', 'description' => 'credit Balance updated by admin', 'received_by' => '1'),
            array('transaction_id' => '109', 'user_id' => '52', 'date' => '2017-07-07 09:10:00', 'amount' => '-299.16', 'description' => 'credit Balance updated by admin', 'received_by' => '1'),
            array('transaction_id' => '110', 'user_id' => '33', 'date' => '2017-07-07 09:10:00', 'amount' => '101.00', 'description' => 'credit Balance updated by admin', 'received_by' => '1'),
            array('transaction_id' => '111', 'user_id' => '47', 'date' => '2017-07-07 09:11:00', 'amount' => '-499.12', 'description' => 'credit Balance updated by admin', 'received_by' => '1'),
            array('transaction_id' => '113', 'user_id' => '43', 'date' => '2017-07-07 09:12:00', 'amount' => '182.00', 'description' => 'credit Balance updated by admin', 'received_by' => '1'),
            array('transaction_id' => '114', 'user_id' => '36', 'date' => '2017-07-07 09:12:00', 'amount' => '1.00', 'description' => 'credit Balance updated by admin', 'received_by' => '1'),
            array('transaction_id' => '115', 'user_id' => '32', 'date' => '2017-07-07 09:13:00', 'amount' => '1.74', 'description' => 'credit Balance updated by admin', 'received_by' => '1'),
            array('transaction_id' => '116', 'user_id' => '20', 'date' => '2017-07-07 09:14:00', 'amount' => '20.95', 'description' => 'credit Balance updated by admin', 'received_by' => '1'),
            array('transaction_id' => '117', 'user_id' => '41', 'date' => '2017-07-07 09:14:00', 'amount' => '96.44', 'description' => 'credit Balance updated by admin', 'received_by' => '1'),
            array('transaction_id' => '118', 'user_id' => '21', 'date' => '2017-07-07 09:15:00', 'amount' => '2.27', 'description' => 'credit Balance updated by admin', 'received_by' => '1'),
            array('transaction_id' => '119', 'user_id' => '30', 'date' => '2017-07-07 09:16:00', 'amount' => '2.94', 'description' => 'credit Balance updated by admin', 'received_by' => '1'),
            array('transaction_id' => '120', 'user_id' => '31', 'date' => '2017-07-07 09:16:00', 'amount' => '5.00', 'description' => 'credit Balance updated by admin', 'received_by' => '1'),
            array('transaction_id' => '121', 'user_id' => '37', 'date' => '2017-07-07 09:17:00', 'amount' => '0.81', 'description' => 'credit Balance updated by admin', 'received_by' => '1'),
            array('transaction_id' => '122', 'user_id' => '19', 'date' => '2017-07-07 09:17:00', 'amount' => '42.16', 'description' => 'credit Balance updated by admin', 'received_by' => '1'),
            array('transaction_id' => '123', 'user_id' => '50', 'date' => '2017-07-07 09:18:00', 'amount' => '0.97', 'description' => 'credit Balance updated by admin', 'received_by' => '1'),
            array('transaction_id' => '124', 'user_id' => '23', 'date' => '2017-07-07 09:19:00', 'amount' => '0.95', 'description' => 'credit Balance updated by admin', 'received_by' => '1'),
            array('transaction_id' => '125', 'user_id' => '18', 'date' => '2017-07-07 09:19:00', 'amount' => '2.00', 'description' => 'credit Balance updated by admin', 'received_by' => '1'),
            array('transaction_id' => '126', 'user_id' => '25', 'date' => '2017-07-07 09:20:00', 'amount' => '2.00', 'description' => 'credit Balance updated by admin', 'received_by' => '1'),
            array('transaction_id' => '127', 'user_id' => '38', 'date' => '2017-07-07 09:20:00', 'amount' => '88.11', 'description' => 'credit Balance updated by admin', 'received_by' => '1'),
            array('transaction_id' => '128', 'user_id' => '34', 'date' => '2017-07-07 09:21:00', 'amount' => '-59.07', 'description' => 'credit Balance updated by admin', 'received_by' => '1'),
            array('transaction_id' => '142', 'user_id' => '65', 'date' => '2017-07-19 14:08:00', 'amount' => '200.00', 'description' => 'test amount ', 'received_by' => '53'),
            array('transaction_id' => '194', 'user_id' => '53', 'date' => '2017-08-01 11:30:00', 'amount' => '150.00', 'description' => 'test', 'received_by' => '53'),
            array('transaction_id' => '195', 'user_id' => '53', 'date' => '2017-08-01 11:40:00', 'amount' => '150.00', 'description' => 'test', 'received_by' => '53'),
            array('transaction_id' => '247', 'user_id' => '67', 'date' => '2017-08-04 15:39:00', 'amount' => '20.00', 'description' => 'intial balance', 'received_by' => '53'),
            array('transaction_id' => '274', 'user_id' => '66', 'date' => '2017-08-11 07:18:00', 'amount' => '500.00', 'description' => 'Bank transfer by credit', 'received_by' => '53'),
            array('transaction_id' => '443', 'user_id' => '69', 'date' => '2017-09-23 10:09:19', 'amount' => '10.00', 'description' => NULL, 'received_by' => '53'),
            array('transaction_id' => '484', 'user_id' => '66', 'date' => '2017-10-16 21:11:09', 'amount' => '500.00', 'description' => NULL, 'received_by' => '46'),
            array('transaction_id' => '537', 'user_id' => '77', 'date' => '2017-10-27 18:33:47', 'amount' => '30.00', 'description' => NULL, 'received_by' => '53'),
            array('transaction_id' => '583', 'user_id' => '69', 'date' => '2017-11-03 06:45:32', 'amount' => '90.00', 'description' => NULL, 'received_by' => '53'),
            array('transaction_id' => '751', 'user_id' => '66', 'date' => '2017-11-16 16:30:15', 'amount' => '500.00', 'description' => NULL, 'received_by' => '46'),
            array('transaction_id' => '791', 'user_id' => '66', 'date' => '2017-11-22 11:53:41', 'amount' => '500.00', 'description' => NULL, 'received_by' => '46'),
            array('transaction_id' => '846', 'user_id' => '94', 'date' => '2017-11-27 09:05:36', 'amount' => '100.00', 'description' => NULL, 'received_by' => '65'),
            array('transaction_id' => '857', 'user_id' => '102', 'date' => '2017-11-27 17:26:42', 'amount' => '50.00', 'description' => NULL, 'received_by' => '53'),
            array('transaction_id' => '898', 'user_id' => '107', 'date' => '2017-12-01 18:07:26', 'amount' => '5.00', 'description' => NULL, 'received_by' => '102'),
            array('transaction_id' => '953', 'user_id' => '107', 'date' => '2017-12-07 17:03:36', 'amount' => '45.00', 'description' => NULL, 'received_by' => '102'),
            array('transaction_id' => '955', 'user_id' => '112', 'date' => '2017-12-07 17:13:29', 'amount' => '5.00', 'description' => NULL, 'received_by' => '102'),
            array('transaction_id' => '964', 'user_id' => '107', 'date' => '2017-12-07 18:26:46', 'amount' => '140.00', 'description' => NULL, 'received_by' => '102'),
            array('transaction_id' => '974', 'user_id' => '113', 'date' => '2017-12-08 16:28:50', 'amount' => '5.00', 'description' => NULL, 'received_by' => '102'),
            array('transaction_id' => '975', 'user_id' => '114', 'date' => '2017-12-08 16:34:06', 'amount' => '5.00', 'description' => NULL, 'received_by' => '102'),
            array('transaction_id' => '977', 'user_id' => '115', 'date' => '2017-12-08 19:33:52', 'amount' => '5.00', 'description' => NULL, 'received_by' => '102'),
            array('transaction_id' => '1003', 'user_id' => '66', 'date' => '2017-12-10 14:21:40', 'amount' => '500.00', 'description' => NULL, 'received_by' => '46'),
            array('transaction_id' => '1016', 'user_id' => '102', 'date' => '2017-12-12 01:14:26', 'amount' => '250.00', 'description' => NULL, 'received_by' => '46'),
            array('transaction_id' => '1022', 'user_id' => '120', 'date' => '2017-12-13 08:35:21', 'amount' => '70.00', 'description' => NULL, 'received_by' => '53'),
            array('transaction_id' => '1023', 'user_id' => '121', 'date' => '2017-12-13 08:39:54', 'amount' => '80.00', 'description' => NULL, 'received_by' => '53'),
            array('transaction_id' => '1024', 'user_id' => '120', 'date' => '2017-12-13 08:40:46', 'amount' => '30.00', 'description' => NULL, 'received_by' => '53'),
            array('transaction_id' => '1025', 'user_id' => '122', 'date' => '2017-12-13 08:45:03', 'amount' => '50.00', 'description' => NULL, 'received_by' => '53'),
            array('transaction_id' => '1071', 'user_id' => '113', 'date' => '2017-12-18 14:46:50', 'amount' => '10.00', 'description' => NULL, 'received_by' => '102'),
            array('transaction_id' => '1083', 'user_id' => '113', 'date' => '2017-12-21 09:25:25', 'amount' => '85.00', 'description' => NULL, 'received_by' => '102'),
            array('transaction_id' => '1135', 'user_id' => '30', 'date' => '2017-12-30 17:48:10', 'amount' => '50.00', 'description' => NULL, 'received_by' => '46'),
            array('transaction_id' => '1160', 'user_id' => '66', 'date' => '2018-01-03 00:43:54', 'amount' => '600.00', 'description' => NULL, 'received_by' => '46'),
            array('transaction_id' => '1250', 'user_id' => '114', 'date' => '2018-01-09 18:20:08', 'amount' => '10.00', 'description' => NULL, 'received_by' => '102'),
            array('transaction_id' => '1273', 'user_id' => '66', 'date' => '2018-01-10 18:27:33', 'amount' => '600.00', 'description' => NULL, 'received_by' => '46'),
            array('transaction_id' => '1486', 'user_id' => '66', 'date' => '2018-01-25 10:54:26', 'amount' => '600.00', 'description' => NULL, 'received_by' => '46'),
            array('transaction_id' => '1511', 'user_id' => '30', 'date' => '2018-01-29 11:45:57', 'amount' => '50.00', 'description' => NULL, 'received_by' => '46'),
            array('transaction_id' => '1631', 'user_id' => '114', 'date' => '2018-02-02 10:32:26', 'amount' => '50.00', 'description' => NULL, 'received_by' => '102'),
            array('transaction_id' => '1674', 'user_id' => '135', 'date' => '2018-02-02 20:19:45', 'amount' => '155.00', 'description' => NULL, 'received_by' => '134'),
            array('transaction_id' => '1818', 'user_id' => '66', 'date' => '2018-02-07 21:46:30', 'amount' => '600.00', 'description' => NULL, 'received_by' => '46'),
            array('transaction_id' => '2158', 'user_id' => '66', 'date' => '2018-02-11 13:25:00', 'amount' => '500.00', 'description' => NULL, 'received_by' => '46'),
            array('transaction_id' => '2339', 'user_id' => '177', 'date' => '2018-02-12 18:50:55', 'amount' => '500.00', 'description' => NULL, 'received_by' => '134'),
            array('transaction_id' => '2629', 'user_id' => '135', 'date' => '2018-02-14 12:46:42', 'amount' => '500.00', 'description' => NULL, 'received_by' => '134'),
            array('transaction_id' => '3374', 'user_id' => '30', 'date' => '2018-02-18 11:57:43', 'amount' => '50.00', 'description' => NULL, 'received_by' => '46'),
            array('transaction_id' => '4142', 'user_id' => '135', 'date' => '2018-02-21 21:31:20', 'amount' => '18.40', 'description' => NULL, 'received_by' => '134'),
            array('transaction_id' => '4143', 'user_id' => '135', 'date' => '2018-02-21 21:37:21', 'amount' => '500.00', 'description' => NULL, 'received_by' => '134'),
            array('transaction_id' => '5630', 'user_id' => '66', 'date' => '2018-02-28 08:07:04', 'amount' => '500.00', 'description' => NULL, 'received_by' => '46'),
            array('transaction_id' => '7174', 'user_id' => '66', 'date' => '2018-03-05 09:56:34', 'amount' => '200.00', 'description' => NULL, 'received_by' => '46'),
            array('transaction_id' => '8012', 'user_id' => '66', 'date' => '2018-03-07 15:54:06', 'amount' => '500.00', 'description' => NULL, 'received_by' => '46'),
            array('transaction_id' => '8540', 'user_id' => '30', 'date' => '2018-03-09 10:42:43', 'amount' => '100.00', 'description' => NULL, 'received_by' => '46'),
            array('transaction_id' => '10212', 'user_id' => '66', 'date' => '2018-03-14 00:18:32', 'amount' => '500.00', 'description' => NULL, 'received_by' => '46'),
            array('transaction_id' => '11336', 'user_id' => '206', 'date' => '2018-03-16 22:56:51', 'amount' => '5.00', 'description' => NULL, 'received_by' => '67'),
            array('transaction_id' => '12976', 'user_id' => '135', 'date' => '2018-03-21 19:33:53', 'amount' => '-525.00', 'description' => NULL, 'received_by' => '134'),
            array('transaction_id' => '12977', 'user_id' => '177', 'date' => '2018-03-21 19:36:25', 'amount' => '-320.00', 'description' => NULL, 'received_by' => '134'),
            array('transaction_id' => '14158', 'user_id' => '66', 'date' => '2018-03-25 13:41:00', 'amount' => '500.00', 'description' => NULL, 'received_by' => '46'),
            array('transaction_id' => '17446', 'user_id' => '248', 'date' => '2018-04-01 11:25:52', 'amount' => '10.00', 'description' => NULL, 'received_by' => '102')
        );
        Payment::insert($transactions);
        return true;
    }

    static function migrate_calling_cards_access()
    {
        $tb_myservices = array(
            array('id' => '15', 'access_data' => '{"allowed_resellers":[117,118,124,103,123,134,139,140,141,125,126,127,128,129,130,131,132,133,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '18', 'access_data' => '{"allowed_resellers":[117,124,118,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '19', 'access_data' => '{"allowed_resellers":[117,124,118,103,123,134,140,139,141,125,126,127,128,129,130,131,132,133,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '22', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '23', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '24', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '25', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,139,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '26', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '27', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,139,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '28', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '29', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,139,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '30', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '31', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,139,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '32', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '33', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '34', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '35', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,139,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '36', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '37', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '38', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,139,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '39', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '40', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,139,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '41', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '42', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,139,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '43', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '44', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '45', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '46', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '47', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '48', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '49', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '50', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,139,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '51', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '52', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '53', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,139,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '54', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '55', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,139,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '57', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '58', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,139,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '59', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '60', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '61', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '62', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '64', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '65', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '66', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '67', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '68', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '69', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '70', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '71', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '72', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '73', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '74', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '75', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '76', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '77', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '80', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,124,135,134,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '85', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,140,143,124,135,134,139,141,146,142,144,145,157,147,148,149,150,151,152,153,154,155,156,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '86', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,140,143,163,165,172,173,174,175,176,182,183,184,124,135,177,134,139,141,146,161,162,164,166,167,168,170,179,180,181,142,144,145,157,147,148,149,150,151,152,153,154,155,156,171,169,178,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '87', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,140,143,163,165,172,173,174,175,176,182,183,184,124,135,177,134,139,141,146,161,162,164,166,167,168,170,179,180,181,142,144,145,157,147,148,149,150,151,152,153,154,155,156,171,169,178,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '88', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,140,143,163,165,172,173,174,175,176,182,183,184,124,135,177,134,139,141,146,161,162,164,166,167,168,170,179,180,181,142,144,145,157,147,148,149,150,151,152,153,154,155,156,171,169,178,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '89', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,140,143,163,165,172,173,174,175,176,182,183,184,124,135,177,134,139,141,146,161,162,164,166,167,168,170,179,180,181,142,144,145,157,147,148,149,150,151,152,153,154,155,156,171,169,178,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '90', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,134,140,143,163,165,172,173,174,175,176,182,183,184,188,190,191,192,193,197,124,139,141,146,161,162,164,166,167,168,170,179,180,181,187,189,194,195,196,142,144,145,157,147,148,149,150,151,152,153,154,155,156,171,169,185,186,178,198,199,200,201,202,203,204,205,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,249,250,251,252,253,254,255,256,257,258,259]}'),
            array('id' => '91', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,134,140,143,163,165,172,173,174,175,176,182,183,184,188,190,191,192,193,197,211,227,230,124,139,141,146,161,162,164,166,167,168,170,179,180,181,187,189,194,195,196,199,200,209,223,247,142,144,145,157,205,207,147,148,149,150,151,152,153,154,155,156,171,169,185,186,202,178,201,203,204,198,210,212,213,214,215,216,217,218,219,220,221,222,232,234,242,243,244,245,249,250,251,252,253,254,255,208,225,231,233,224,228,229,235,236,237,238,239,240,241,246,226,256,257,258,259]}'),
            array('id' => '93', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,134,140,143,163,165,172,173,174,175,176,182,183,184,188,190,191,192,193,197,211,227,230,124,139,141,146,161,162,164,166,167,168,170,179,180,181,187,189,194,195,196,199,200,209,223,247,142,144,145,157,205,207,147,148,149,150,151,152,153,154,155,156,171,169,185,186,202,178,201,203,204,198,210,212,213,214,215,216,217,218,219,220,221,222,232,234,242,243,244,245,249,250,251,252,253,254,255,208,225,231,233,224,228,229,235,236,237,238,239,240,241,246,226,256,257,258,259]}'),
            array('id' => '94', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,134,140,143,163,165,172,173,174,175,176,182,183,184,188,190,191,192,193,197,211,227,230,124,139,141,146,161,162,164,166,167,168,170,179,180,181,187,189,194,195,196,199,200,209,223,247,142,144,145,157,205,207,147,148,149,150,151,152,153,154,155,156,171,169,185,186,202,178,201,203,204,198,210,212,213,214,215,216,217,218,219,220,221,222,232,234,242,243,244,245,249,250,251,252,253,254,255,257,208,225,231,233,224,228,229,235,236,237,238,239,240,241,246,258,226,256,259]}'),
            array('id' => '95', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,134,140,143,163,165,172,173,174,175,176,182,183,184,188,190,191,192,193,197,211,227,230,124,139,141,146,161,162,164,166,167,168,170,179,180,181,187,189,194,195,196,199,200,209,223,247,142,144,145,157,205,207,147,148,149,150,151,152,153,154,155,156,171,169,185,186,202,178,201,203,204,198,210,212,213,214,215,216,217,218,219,220,221,222,232,234,242,243,244,245,249,250,251,252,253,254,255,257,208,225,231,233,224,228,229,235,236,237,238,239,240,241,246,258,226,256,259]}'),
            array('id' => '96', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,134,140,143,163,165,172,173,174,175,176,182,183,184,188,190,191,192,193,197,211,227,230,124,139,141,146,161,162,164,166,167,168,170,179,180,181,187,189,194,195,196,199,200,209,223,247,142,144,145,157,205,207,147,148,149,150,151,152,153,154,155,156,171,169,185,186,202,178,201,203,204,198,210,212,213,214,215,216,217,218,219,220,221,222,232,234,242,243,244,245,249,250,251,252,253,254,255,257,208,225,231,233,224,228,229,235,236,237,238,239,240,241,246,258,226,256,259]}'),
            array('id' => '97', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,134,140,143,163,165,172,173,174,175,176,182,183,184,188,190,191,192,193,197,211,227,230,124,139,141,146,161,162,164,166,167,168,170,179,180,181,187,189,194,195,196,199,200,209,223,247,142,144,145,157,205,207,147,148,149,150,151,152,153,154,155,156,171,169,185,186,202,178,201,203,204,198,210,212,213,214,215,216,217,218,219,220,221,222,232,234,242,243,244,245,249,250,251,252,253,254,255,257,208,225,231,233,224,228,229,235,236,237,238,239,240,241,246,258,226,256,259]}'),
            array('id' => '98', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,134,140,143,163,165,172,173,174,175,176,182,183,184,188,190,191,192,193,197,211,227,230,124,139,141,146,161,162,164,166,167,168,170,179,180,181,187,189,194,195,196,199,200,209,223,247,142,144,145,157,205,207,147,148,149,150,151,152,153,154,155,156,171,169,185,186,202,178,201,203,204,198,210,212,213,214,215,216,217,218,219,220,221,222,232,234,242,243,244,245,249,250,251,252,253,254,255,257,208,225,231,233,224,228,229,235,236,237,238,239,240,241,246,258,226,256,259]}'),
            array('id' => '99', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,134,140,143,163,165,172,173,174,175,176,182,183,184,188,190,191,192,193,197,211,227,230,124,139,141,146,161,162,164,166,167,168,170,179,180,181,187,189,194,195,196,199,200,209,223,247,142,144,145,157,205,207,147,148,149,150,151,152,153,154,155,156,171,169,185,186,202,178,201,203,204,198,210,212,213,214,215,216,217,218,219,220,221,222,232,234,242,243,244,245,249,250,251,252,253,254,255,257,208,225,231,233,224,228,229,235,236,237,238,239,240,241,246,258,226,256,259]}'),
            array('id' => '101', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,134,140,143,163,165,172,173,174,175,176,182,183,184,188,190,191,192,193,197,211,227,230,124,139,141,146,161,162,164,166,167,168,170,179,180,181,187,189,194,195,196,199,200,209,223,247,142,144,145,157,205,207,147,148,149,150,151,152,153,154,155,156,171,169,185,186,202,178,201,203,204,198,210,212,213,214,215,216,217,218,219,220,221,222,232,234,242,243,244,245,249,250,251,252,253,254,255,257,208,225,231,233,224,228,229,235,236,237,238,239,240,241,246,258,226,256,259]}'),
            array('id' => '102', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,134,140,143,163,165,172,173,174,175,176,182,183,184,188,190,191,192,193,197,211,227,230,124,139,141,146,161,162,164,166,167,168,170,179,180,181,187,189,194,195,196,199,200,209,223,247,142,144,145,157,205,207,147,148,149,150,151,152,153,154,155,156,171,169,185,186,202,178,201,203,204,198,210,212,213,214,215,216,217,218,219,220,221,222,232,234,242,243,244,245,249,250,251,252,253,254,255,257,208,225,231,233,224,228,229,235,236,237,238,239,240,241,246,258,226,256,259]}'),
            array('id' => '103', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,134,140,143,163,165,172,173,174,175,176,182,183,184,188,190,191,192,193,197,211,227,230,124,139,141,146,161,162,164,166,167,168,170,179,180,181,187,189,194,195,196,199,200,209,223,247,142,144,145,157,205,207,147,148,149,150,151,152,153,154,155,156,171,169,185,186,202,178,201,203,204,198,210,212,213,214,215,216,217,218,219,220,221,222,232,234,242,243,244,245,249,250,251,252,253,254,255,257,208,225,231,233,224,228,229,235,236,237,238,239,240,241,246,258,226,256,259]}'),
            array('id' => '104', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,134,140,143,163,165,172,173,174,175,176,182,183,184,188,190,191,192,193,197,211,227,230,124,139,141,146,161,162,164,166,167,168,170,179,180,181,187,189,194,195,196,199,200,209,223,247,142,144,145,157,205,207,147,148,149,150,151,152,153,154,155,156,171,169,185,186,202,178,201,203,204,198,210,212,213,214,215,216,217,218,219,220,221,222,232,234,242,243,244,245,249,250,251,252,253,254,255,257,208,225,231,233,224,228,229,235,236,237,238,239,240,241,246,258,226,256,259]}'),
            array('id' => '105', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,134,140,143,163,165,172,173,174,175,176,182,183,184,188,190,191,192,193,197,211,227,230,124,139,141,146,161,162,164,166,167,168,170,179,180,181,187,189,194,195,196,199,200,209,223,247,142,144,145,157,205,207,147,148,149,150,151,152,153,154,155,156,171,169,185,186,202,178,201,203,204,198,210,212,213,214,215,216,217,218,219,220,221,222,232,234,242,243,244,245,249,250,251,252,253,254,255,257,208,225,231,233,224,228,229,235,236,237,238,239,240,241,246,258,226,256,259]}'),
            array('id' => '106', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,134,140,143,163,165,172,173,174,175,176,182,183,184,188,190,191,192,193,197,211,227,230,124,139,141,146,161,162,164,166,167,168,170,179,180,181,187,189,194,195,196,199,200,209,223,247,142,144,145,157,205,207,147,148,149,150,151,152,153,154,155,156,171,169,185,186,202,178,201,203,204,198,210,212,213,214,215,216,217,218,219,220,221,222,232,234,242,243,244,245,249,250,251,252,253,254,255,257,208,225,231,233,224,228,229,235,236,237,238,239,240,241,246,258,226,256,259]}'),
            array('id' => '108', 'access_data' => '{"allowed_resellers":[104,103,118,117,123,125,126,127,128,129,130,131,132,133,134,140,143,163,165,172,173,174,175,176,182,183,184,188,190,191,192,193,197,211,227,230,124,139,141,146,161,162,164,166,167,168,170,179,180,181,187,189,194,195,196,199,200,209,223,247,142,144,145,157,205,207,147,148,149,150,151,152,153,154,155,156,171,169,185,186,202,178,201,203,204,198,210,212,213,214,215,216,217,218,219,220,221,222,232,234,242,243,244,245,249,250,251,252,253,254,255,257,208,225,231,233,224,228,229,235,236,237,238,239,240,241,246,258,226,256,259]}')
        );
        foreach ($tb_myservices as $item) {
            $cc_id = $item['id'];
            $decoded_access = json_decode($item['access_data']);
            foreach ($decoded_access->allowed_resellers as $key => $user) {
                $user_check = User::find($user);
                if ($user_check) {
                    CallingCardAccess::insert([
                        'cc_id' => $cc_id,
                        'user_id' => $user,
                        'status' => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => auth()->user()->id
                    ]);
                }
            }
        }
        return true;
    }


    static function migrate_orders()
    {
//        return null;
        $path = public_path('tamaapp_orders.csv');
//        echo "Loading file...<br>";
//        $read = Excel::load($path, function ($reader) {
//        })->get();
//        $data = OrderSeedHelper::get_orders(2);
//        $data = collect($read)->toArray();
//        dd($data);
        Excel::filter('chunk')->load($path)->chunk(200, function ($results) use (&$data) {
            try{
                \DB::beginTransaction();
                \DB::statement('SET FOREIGN_KEY_CHECKS=0');
                $collection = collect($results);
                foreach ($results as $order) {
//                    dd($order['user_id']);
                    $user = User::find($order['user_id']);
                    if ($user) {
                        //for tama app only pms order so skip_order will be 1
                        if($order['skip_order'] == 1){
                            $buying_price = $order['order_amount'] - $order['sale_margin'];
                            $sale_margin = $order['sale_margin'];
//                            dd($order['up_timestamp']);
                            Order::insertGetId([
                                'id' => $order['id'],
                                'date' => $order['created_at'],
                                'user_id' => $order['user_id'],
                                'service_id' => $order['service_id'],
                                'order_status_id' => $order['order_status_id'],
                                'transaction_id' => $order['transaction_id'],
                                'txn_ref' => $order['txn_id'],
                                'comment' => $order['order_comment'],
                                'public_price' => $order['public_price'],
                                'buying_price' => $order['buying_price'] == "NULL" ? 0 : $order['buying_price'],
                                'sale_margin' => $sale_margin,
                                'order_amount' => $order['order_amount'],
                                'grand_total' => $order['order_amount'],
                                'currency' => $order['order_currency'],
                                'created_at' => $order['created_at'],
                                'created_by' => $order['created_by'] == "NULL" ? null : $order['created_by'],
                                'updated_at' => $order['updated_at'] == "NULL" ? $order['up_timestamp'] : $order['updated_at'],
                                'updated_by' => $order['updated_by'] == "NULL" ? null : $order['updated_by'],
                            ]);
                            $order_item_id = OrderItem::insertGetId([
                                'order_id' => $order['id'],
                                'product_id' => $order['product_id'] == "NULL" ? null : $order['product_id'],
                                'sender_first_name' => $order['sender_fname'] == "NULL" ? null : $order['sender_fname'],
                                'sender_last_name' => $order['sender_lname'] == "NULL" ? null : $order['sender_lname'],
                                'sender_mobile' => $order['sender_mobile'] == "NULL" ? null : $order['sender_mobile'],
                                'sender_email' => $order['sender_email'] == "NULL" ? null : $order['sender_email'],
                                'sender_address' => $order['sender_address1'] == "NULL" ? null : $order['sender_address1'],
                                'receiver_first_name' => $order['receiver_fname'] == "NULL" ? null : $order['receiver_fname'],
                                'receiver_last_name' => $order['receiver_lname'] == "NULL" ? null : $order['receiver_lname'],
                                'receiver_mobile' => $order['receiver_mobile'] == "NULL" ? null : $order['receiver_mobile'],
                                'receiver_email' => $order['receiver_email'] == "NULL" ? null : $order['receiver_email'],
                                'receiver_address' => $order['receiver_address1'] == "NULL" ? null : $order['receiver_address1'],
                                'tama_pin' => $order['tama_pin'] == "NULL" ? null : $order['tama_pin'],
                                'tt_mobile' => $order['topup_mobile'] == "NULL" ? null : $order['topup_mobile'],
                                'tt_euro_amount' => $order['topup_euro'] == "NULL" ? 0 : $order['topup_euro'],
                                'tt_dest_amount' => $order['topup_dest_amount'] == "NULL" ? 0 : $order['topup_dest_amount'],
                                'tt_dest_currency' => $order['topup_dest_currency'] == "NULL" ? null : $order['topup_dest_currency'],
                                'tt_operator' => $order['topup_operator'] == "NULL" ? null : $order['topup_operator'],
                                'app_mobile' => $order['topup_mobile'] == "NULL" ? null : $order['topup_mobile'],
                                'app_old_balance' => $order['app_oldbal'] == "NULL" ? "0" : $order['app_oldbal'],
                                'app_new_balance' => $order['app_newbal'] == "NULL" ? "0" : $order['app_newbal'],
                                'app_amount_topup' => $order['app_origin_amount'] == "NULL" ? "0" : str_replace("€", '', str_replace("$", '', $order['app_origin_amount'])),
                                'app_currency' => strpos($order['app_origin_amount'], '$') !== false ? "USD" : "EUR",
                                'created_at' => $order['created_at'],
                                'created_by' => $order['created_by'] == "NULL" ? null : $order['created_by'],
                                'updated_at' => $order['updated_at'] == "NULL" ? $order['up_timestamp'] : $order['updated_at'],
                                'updated_by' => $order['updated_by'] == "NULL" ? null : $order['updated_by'],
                            ]);
                            if($user->parent_id != '' || $user->parent_id != null){
                                $filtered = $collection->where('txn_id',$order['txn_id'])
                                    ->where('user_id',$user->parent_id);
                                $parent_fields = $filtered->values()->all();
                                if(isset($parent_fields[0])){
                                    $parent_field = collect($parent_fields[0]);
//                                    dd($parent_field);
//                                    Log::info('parent fields',$parent_field);
//                                Log::info('bp object',$parent_field->buying_price);
//                                    Log::info('bp array => '.$parent_field['buying_price']);
//                                    Log::info('order array => '.$parent_field['order_amount']);
//                            dd($parent_field['buying_price'] );
                                    Order::insertGetId([
//                                        'id' => $parent_field['id'],
                                        'date' => $order['created_at'],
                                        'user_id' => $order['user_id'],
                                        'service_id' => $order['service_id'],
                                        'order_status_id' => $order['order_status_id'],
                                        'transaction_id' => $order['transaction_id'],
                                        'txn_ref' => $order['txn_id'],
                                        'comment' => $order['order_comment'],
                                        'public_price' => $order['public_price'],
                                        'buying_price' => $parent_field['buying_price'] == "NULL" ? 0 : $parent_field['buying_price'],
                                        'sale_margin' => $order['order_amount'] - $parent_field['buying_price'],
                                        'order_amount' => $parent_field['order_amount'],
                                        'grand_total' => $parent_field['order_amount'],
                                        'is_parent_order' => 1,
                                        'order_item_id' => $order_item_id,
                                        'currency' => $order['order_currency'],
                                        'created_at' => $order['created_at'],
                                        'created_by' => $order['created_by'] == "NULL" ? null : $order['created_by'],
                                        'updated_at' => $order['updated_at'] == "NULL" ? $order['up_timestamp'] : $order['updated_at'],
                                        'updated_by' => $order['updated_by'] == "NULL" ? null : $order['updated_by'],
                                    ]);
                                    $parent_user = User::find($user->parent_id);
                                    $master_user = User::find($parent_user->parent_id);
//                            dd($master_user);
                                    if($master_user){
                                        $master_filter = $collection->where('user_id',$master_user->id)->where('txn_id',$order['txn_id']);
                                        $master_parent_fields = $master_filter->values()->all();
                                        if(isset($master_parent_fields[0])){
                                            $master_parent_field = collect($master_parent_fields[0]);
//                                            dd($master_parent_field);
//                                            Log::info('master parent fields',$master_parent_field);
//                                dd($master_parent_field);
                                            Order::insertGetId([
//                                                'id' => $master_parent_field['id'],
                                                'date' => $order['created_at'],
                                                'user_id' => $user->parent_id,
                                                'service_id' => $order['service_id'],
                                                'order_status_id' => $order['order_status_id'],
                                                'transaction_id' => $order['transaction_id'],
                                                'txn_ref' => $order['txn_id'],
                                                'comment' => $order['order_comment'],
                                                'public_price' => $order['public_price'],
                                                'buying_price' => $master_parent_field['buying_price'] == null ? 0 : $master_parent_field['buying_price'],
                                                'sale_margin' => $parent_field['order_amount'] - $master_parent_field['buying_price'],
                                                'order_amount' => $parent_field['order_amount'],
                                                'grand_total' => $parent_field['order_amount'],
                                                'is_parent_order' => 1,
                                                'order_item_id' => $order_item_id,
                                                'currency' => $order['order_currency'],
                                                'created_at' => $order['created_at'],
                                                'created_by' => $order['created_by'] == "NULL" ? null : $order['created_by'],
                                                'updated_at' => $order['updated_at'] == "NULL" ? $order['up_timestamp'] : $order['updated_at'],
                                                'updated_by' => $order['updated_by'] == "NULL" ? null : $order['updated_by'],
                                            ]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                \DB::commit();
                echo "<br>Inserted ";
                Log::info('migrated inserted');
            }catch (\Exception $e){
                \DB::rollBack();
                Log::warning('Exception => '.$e->getMessage()." line => ".$e->getLine());
                echo "Exception ".$e->getMessage()." Line ".$e->getLine();
            }
        }, $shouldQueue = true);



    }

    static function migrate_transactions()
    {
        $path = public_path('transactions.csv');
        Excel::load($path)->chunk(200, function ($data) {
            $transactions = [];
            $counter = 1;
            foreach ($data as $trans) {
                $transactions[] = [
                    'id' => $trans['id'],
                    'user_id' => $trans['user_id'],
                    'date' => $trans['trans_date'],
                    'type' => $trans['trans_type'],
                    'amount' => $trans['amount'] == "NULL" ? 0 : $trans['amount'],
                    'debit' => $trans['dr'] == "NULL" ? 0 : $trans['dr'],
                    'credit' => $trans['cr'] == "NULL" ? 0 : $trans['cr'],
                    'prev_bal' => $trans['prev_bal'] == "NULL" ? 0 : $trans['prev_bal'],
                    'balance' => $trans['balance'] == "NULL" ? 0 : $trans['balance'],
                    'margin' => $trans['sale_margin'] == "NULL" ? 0 : $trans['sale_margin'],
                    'is_exclude' => $trans['is_exclude'],
                    'description' => $trans['description'],
                    'created_at' => $trans['created_at'] == "NULL" ? null : $trans['created_at'],
                    'created_by' => $trans['created_by'] == "NULL" ? null : $trans['created_by']
                ];
                $counter++;
            }
            // insert into db
            \DB::statement('SET FOREIGN_KEY_CHECKS=0');
            Transaction::insert($transactions);
            echo "Inserted $counter transactions <br>";
        });
    }

    static function migrate_payment_history()
    {
        $payments = Transaction::join('users', 'users.id', 'transactions.user_id')
            ->where('transactions.type', 'credit')
            ->select([
                'transactions.id',
                'transactions.user_id',
                'transactions.date',
                'transactions.amount',
                'transactions.description',
                'transactions.created_by'
            ])->get();
        $counter = 1;
        foreach ($payments as $payment) {
            Payment::insert([
                'user_id' => $payment->user_id,
                'transaction_id' => $payment->id,
                'date' => $payment->date,
                'amount' => $payment->amount,
                'description' => $payment->description == "NULL" ? null : $payment->description,
                'received_by' => $payment->created_by
            ]);
            $counter++;
        }
        echo "$counter payments inserted";
    }

    static function migrate_cc_orders()
    {
//        return null;
        $path = public_path('cc_mar_order.csv');
//        echo "Loading file...<br>";
//        $read = Excel::load($path, function ($reader) {
//        })->get();
//        $data = OrderSeedHelper::get_orders(2);
//        $data = collect($read)->toArray();
//        dd($data);
        Excel::filter('chunk')->load($path)->chunk(600, function ($results) use (&$data) {
            try{
                \DB::beginTransaction();
                \DB::statement('SET FOREIGN_KEY_CHECKS=0');
                $collection = collect($results);
                foreach ($results as $order) {
//                    dd($order['user_id']);
                    $user = User::find($order['user_id']);
                    if ($user) {
                        //for tama app only pms order so skip_order will be 1
                        if($order['skip_order'] == 0){
                            $buying_price = $order['order_amount'] - $order['sale_margin'];
                            $sale_margin = $order['sale_margin'];
//                            dd($order['up_timestamp']);
                            Order::insertGetId([
                                'id' => $order['id'],
                                'date' => $order['created_at'],
                                'user_id' => $order['user_id'],
                                'service_id' => $order['service_id'],
                                'order_status_id' => $order['order_status_id'],
                                'transaction_id' => $order['transaction_id'],
                                'txn_ref' => $order['txn_id'],
                                'comment' => $order['order_comment'],
                                'public_price' => $order['public_price'],
                                'buying_price' =>  $order['order_amount'] -  $sale_margin,
                                'sale_margin' => $sale_margin,
                                'order_amount' => $order['order_amount'],
                                'grand_total' => $order['order_amount'],
                                'currency' => $order['order_currency'],
                                'created_at' => $order['created_at'],
                                'created_by' => $order['created_by'] == "NULL" ? null : $order['created_by'],
                                'updated_at' => $order['updated_at'] == "NULL" ? $order['up_timestamp'] : $order['updated_at'],
                                'updated_by' => $order['updated_by'] == "NULL" ? null : $order['updated_by'],
                            ]);
                            $order_item_id = OrderItem::insertGetId([
                                'order_id' => $order['id'],
                                'product_id' => $order['product_id'] == "NULL" ? null : $order['product_id'],
                                'sender_first_name' => $order['sender_fname'] == "NULL" ? null : $order['sender_fname'],
                                'sender_last_name' => $order['sender_lname'] == "NULL" ? null : $order['sender_lname'],
                                'sender_mobile' => $order['sender_mobile'] == "NULL" ? null : $order['sender_mobile'],
                                'sender_email' => $order['sender_email'] == "NULL" ? null : $order['sender_email'],
                                'sender_address' => $order['sender_address1'] == "NULL" ? null : $order['sender_address1'],
                                'receiver_first_name' => $order['receiver_fname'] == "NULL" ? null : $order['receiver_fname'],
                                'receiver_last_name' => $order['receiver_lname'] == "NULL" ? null : $order['receiver_lname'],
                                'receiver_mobile' => $order['receiver_mobile'] == "NULL" ? null : $order['receiver_mobile'],
                                'receiver_email' => $order['receiver_email'] == "NULL" ? null : $order['receiver_email'],
                                'receiver_address' => $order['receiver_address1'] == "NULL" ? null : $order['receiver_address1'],
                                'tama_pin' => $order['tama_pin'] == "NULL" ? null : $order['tama_pin'],
                                'tt_mobile' => $order['topup_mobile'] == "NULL" ? null : $order['topup_mobile'],
                                'tt_euro_amount' => $order['topup_euro'] == "NULL" ? 0 : $order['topup_euro'],
                                'tt_dest_amount' => $order['topup_dest_amount'] == "NULL" ? 0 : $order['topup_dest_amount'],
                                'tt_dest_currency' => $order['topup_dest_currency'] == "NULL" ? null : $order['topup_dest_currency'],
                                'tt_operator' => $order['topup_operator'] == "NULL" ? null : $order['topup_operator'],
                                'app_mobile' => $order['topup_mobile'] == "NULL" ? null : $order['topup_mobile'],
                                'app_old_balance' => $order['app_oldbal'] == "NULL" ? "0" : $order['app_oldbal'],
                                'app_new_balance' => $order['app_newbal'] == "NULL" ? "0" : $order['app_newbal'],
                                'app_amount_topup' => $order['app_origin_amount'] == "NULL" ? "0" : str_replace("€", '', str_replace("$", '', $order['app_origin_amount'])),
                                'app_currency' => strpos($order['app_origin_amount'], '$') !== false ? "USD" : "EUR",
                                'created_at' => $order['created_at'],
                                'created_by' => $order['created_by'] == "NULL" ? null : $order['created_by'],
                                'updated_at' => $order['updated_at'] == "NULL" ? $order['up_timestamp'] : $order['updated_at'],
                                'updated_by' => $order['updated_by'] == "NULL" ? null : $order['updated_by'],
                            ]);
                            if($user->parent_id != '' || $user->parent_id != null){
                                $filtered = $collection->where('txn_id',$order['txn_id'])
                                    ->where('user_id',$user->parent_id);
                                $parent_fields = $filtered->values()->all();
                                if(isset($parent_fields[0])){
                                    $parent_field = collect($parent_fields[0]);
//                                    dd($parent_field);
//                                    Log::info('parent fields',$parent_field);
//                                Log::info('bp object',$parent_field->buying_price);
//                                    Log::info('bp array => '.$parent_field['buying_price']);
//                                    Log::info('order array => '.$parent_field['order_amount']);
//                            dd($parent_field['buying_price'] );
                                    Order::insertGetId([
//                                        'id' => $parent_field['id'],
                                        'date' => $order['created_at'],
                                        'user_id' => $order['user_id'],
                                        'service_id' => $order['service_id'],
                                        'order_status_id' => $order['order_status_id'],
                                        'transaction_id' => $order['transaction_id'],
                                        'txn_ref' => $order['txn_id'],
                                        'comment' => $order['order_comment'],
                                        'public_price' => $order['public_price'],
                                        'buying_price' => $order['buying_price'] == "NULL" ? 0 : $order['buying_price'],
                                        'sale_margin' => $order['order_amount'] - $parent_field['order_amount'],
                                        'order_amount' => $order['order_amount'],
                                        'grand_total' => $order['order_amount'],
                                        'is_parent_order' => 1,
                                        'order_item_id' => $order_item_id,
                                        'currency' => $order['order_currency'],
                                        'created_at' => $order['created_at'],
                                        'created_by' => $order['created_by'] == "NULL" ? null : $order['created_by'],
                                        'updated_at' => $order['updated_at'] == "NULL" ? $order['up_timestamp'] : $order['updated_at'],
                                        'updated_by' => $order['updated_by'] == "NULL" ? null : $order['updated_by'],
                                    ]);
                                    $parent_user = User::find($user->parent_id);
                                    $master_user = User::find($parent_user->parent_id);
//                            dd($master_user);
                                    if($master_user){
                                        Order::insertGetId([
//                                                'id' => $master_parent_field['id'],
                                            'date' => $order['created_at'],
                                            'user_id' => $user->parent_id,
                                            'service_id' => $order['service_id'],
                                            'order_status_id' => $order['order_status_id'],
                                            'transaction_id' => $order['transaction_id'],
                                            'txn_ref' => $order['txn_id'],
                                            'comment' => $order['order_comment'],
                                            'public_price' => $order['public_price'],
                                            'buying_price' => $parent_field['buying_price'] == null ? 0 : $parent_field['buying_price'],
                                            'sale_margin' => $parent_field['order_amount'] - $parent_field['buying_price'],
                                            'order_amount' => $parent_field['order_amount'],
                                            'grand_total' => $parent_field['order_amount'],
                                            'is_parent_order' => 1,
                                            'order_item_id' => $order_item_id,
                                            'currency' => $order['order_currency'],
                                            'created_at' => $order['created_at'],
                                            'created_by' => $order['created_by'] == "NULL" ? null : $order['created_by'],
                                            'updated_at' => $order['updated_at'] == "NULL" ? $order['up_timestamp'] : $order['updated_at'],
                                            'updated_by' => $order['updated_by'] == "NULL" ? null : $order['updated_by'],
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
                \DB::commit();
                echo "<br>Inserted ";
                Log::info('migrated inserted');
            }catch (\Exception $e){
                \DB::rollBack();
                Log::warning('Exception => '.$e->getMessage()." line => ".$e->getLine());
                echo "Exception ".$e->getMessage()." Line ".$e->getLine();
            }
        }, $shouldQueue = true);



    }

}