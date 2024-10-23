<?php

namespace App\Enums;

enum ProductDeletionReason: string
{
    // deletion reasons
    case SYNCHRONIZATION = 'Synchronization';
    case MANUALDELETION = 'Manual Deletion'; // if its deleted by a user in the platform
}
