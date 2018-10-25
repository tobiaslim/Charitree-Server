<?php 
namespace App\Models;

abstract class DonationStatus{
    const PENDING = 'Pending';
    const CANCELLED = 'Cancelled';
    const APPROVED = 'Approved';
    const INPROGRESS = 'In Progress';
    const COMPLETED = 'Completed';
}