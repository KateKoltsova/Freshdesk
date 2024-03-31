<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/config.php';

use Koltsova\Freshdesk\Services\ParseTicketService;
use Koltsova\Freshdesk\Services\CsvWriter;
use Koltsova\Freshdesk\Services\FreshDeskService;

try {
    $writer = new CsvWriter($fileName);
    $writer->writeRow(array_keys($headers));

    $freshDeskService = new FreshdeskService($domain, $apiKey);
    $agents = $freshDeskService->getAllAgents();
    $groups = $freshDeskService->getAllGroups();


    $page = 1;
    while (true) {
        $tickets = $freshDeskService->getTickets($page);

        if (empty($tickets)) {
            break;
        }

        foreach ($tickets as $ticket) {
            $ticketModel = new ParseTicketService($headers, $ticket, $agents, $groups, $statuses, $priorities, $freshDeskService);
            $writer->writeRow($ticketModel->fillable);
        }

        $page++;
    }

    echo "Write tickets to CSV file $fileName successful";
} catch (Exception $e) {
    exit('Error: ' . $e->getMessage());
}