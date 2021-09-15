<?php

namespace App\Service\Data;

use DateTime;

class DataManipulationService
{
    public function getData($files)
    {
        $data = [];

        $ff = fopen($files, "r");
        while ($json = fgets($ff)) {
            $items = json_decode($json, false);

            if ($this->calculateTotalOrderValue($items) === 0) {
                continue;
            }

            $collections = [
                'order_id' => $items->order_id,
                'order_datetime' => $this->orderDate($items),
                'total_order_value' => $this->calculateTotalOrderValue($items),
                'average_unit_price' => $this->averageUnitPrice($items),
                'distinct_unit_count' => $this->distinctUnitCount($items),
                'total_unit_count' => $this->totalUnit($items),
                'customer_state' => str_replace('"', '', $items->customer->shipping_address->state),
            ];

            array_push($data, $collections);
        }

        return $data;
    }

    public function orderDate($items)
    {
        $date = new DateTime($items->order_date);

        return $date->format("c");
    }

    public function totalUnit($items)
    {
        return array_sum(array_column($items->items, 'quantity'));
    }

    public function distinctUnitCount($items)
    {
        $unique_array = [];
        foreach ($items->items as $item) {
            $hash = $item->product->product_id;
            $unique_array[$hash] = $item;
        }

        return count($unique_array);
    }

    public function averageUnitPrice($items)
    {
        $result = $this->calculateTotalOrderValue($items);

        $result = round($result / array_sum(array_column($items->items, 'quantity')), 2);

        return $result;
    }

    public function calculateTotalOrderValue($items)
    {
        $result = 0;

        if (!is_null($items->items)) {
            foreach ($items->items as $item) {
                $result += $item->unit_price * $item->quantity;
            }

            if (!empty($items->discounts)) {
                usort($items->discounts, function ($a, $b) {
                    return strcmp($a->priority, $b->priority);
                });

                foreach ($items->discounts as $discount) {
                    if ($discount->type === "PERCENTAGE") {
                        $result = $result - (($discount->value / 100) * $result);
                    } else {
                        $result = $result - $discount->value;
                    }
                }
            }
        }

        return round($result, 2);
    }
}
