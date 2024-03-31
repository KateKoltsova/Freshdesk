<?php

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$domain = $_ENV['FRESHDESK_DOMAIN'];
$apiKey = $_ENV['FRESHDESK_API_KEY'];

$fileName = 'freshdesk-tickets.csv';

$headers = [
    'Ticket ID' => 'id',
    'Description' => 'description',
    'Status' => ['ParseTicketService', 'setStatus', 'status'],
    'Priority' => ['ParseTicketService', 'setPriority', 'priority'],
    'Agent ID' => 'responder_id',
    'Agent Name' => ['ParseTicketService', 'setAgent', 'responder_id'],
    'Agent Email' => ['ParseTicketService', 'setAgent', 'responder_id'],
    'Contact ID' => 'requester_id',
    'Contact Name' => ['ParseTicketService', 'setContact', ''],
    'Contact Email' => ['ParseTicketService', 'setContact', ''],
    'Group ID' => 'group_id',
    'Group Name' => ['ParseTicketService', 'setGroup', 'group_id'],
    'Company ID' => 'company_id',
    'Company Name' => ['ParseTicketService', 'setCompany', ''],
    'Comments' => ['ParseTicketService', 'setComments', 'id'],
];

$statuses = [
    2 => 'Open',
    3 => 'Pending',
    4 => 'Resolved',
    5 => 'Closed'
];

$priorities = [
    1 => 'Low',
    2 => 'Medium',
    3 => 'High',
    4 => 'Urgent'
];