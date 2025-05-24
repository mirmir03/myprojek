<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['manage/doctor'] = 'doctor';
$route['manage/doctor/view_patient/(:num)'] = 'doctor/view_patient/$1';
$route['manage/doctor/add_comment'] = 'doctor/add_comment';