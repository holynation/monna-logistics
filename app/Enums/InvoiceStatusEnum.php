<?php

namespace App\Enums;

enum InvoiceStatusEnum: string {
	case PENDING = "pending";
	case PROCESSING = "processing";
	case INTRANSIT = "in-transit";
	case CANCELLED = "cancelled";
	case COMPLETED = "completed";
}