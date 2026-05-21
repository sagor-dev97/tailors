<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'api_key',
        'sender_id',
        'api_url',
        'service_status',
        'admission_status',
        'sms_format',
        'sms_json',
        'status_labels',
    ];

    protected $casts = [
        'service_status' => 'boolean',
        'admission_status' => 'boolean',
        'templates_json' => 'array',
        'status_labels' => 'array'
    ];
    public function getTemplate($status)
    {
        // প্রথমে JSON থেকে নেয়ার চেষ্টা
        if ($this->templates_json && isset($this->templates_json[$status])) {
            return $this->templates_json[$status];
        }

        // না পেলে পুরোনো কলাম থেকে নেবে
        $templateColumn = $status . '_template';
        if (isset($this->$templateColumn)) {
            return $this->$templateColumn;
        }

        // কিছুই না পেলে ডিফল্ট
        return $this->sms_text;
    }

    // সব টেমপ্লেট পাওয়া
    public function getAllTemplates()
    {
        if ($this->templates_json) {
            return $this->templates_json;
        }

        // পুরোনো ডাটা থেকে JSON বানানো
        return [
            'pending' => $this->pending_template,
            'processing' => $this->processing_template,
            'shipped' => $this->shipped_template,
            'delivered' => $this->delivered_template,
            'completed' => $this->completed_template,
            'cancelled' => $this->cancelled_template,
        ];
    }

    // টেমপ্লেট সেভ করার ফাংশন
    public function saveTemplates($templates)
    {
        $this->templates_json = $templates;

        // ব্যাকওয়ার্ড কম্প্যাটিবিলিটির জন্য পুরোনো কলামও আপডেট
        foreach ($templates as $status => $template) {
            $column = $status . '_template';
            if (in_array($column, (new \ReflectionClass($this))->getProperties())) {
                $this->$column = $template;
            }
        }

        return $this->save();
    }

    // মেসেজ জেনারেট (বিদ্যমান ফাংশন)
    public function generateMessage($status, $order)
    {
        $template = $this->getTemplate($status);

        $banglaStatus = [
            'pending' => 'অপেক্ষমান',
            'processing' => 'প্রক্রিয়াধীন',
            'shipped' => 'পাঠানো হয়েছে',
            'delivered' => 'ডেলিভারি হয়েছে',
            'completed' => 'সম্পন্ন হয়েছে',
            'cancelled' => 'বাতিল করা হয়েছে'
        ];

        $replacements = [
            '{company}' => $this->sender ?? 'আমাদের কোম্পানি',
            '{order_number}' => $order->order_number ?? '',
            '{status}' => $status,
            '{bangla_status}' => ($this->status_labels[$status] ?? $banglaStatus[$status] ?? $status),
            '{customer_name}' => $order->customer->name ?? 'গ্রাহক',
            '{customer_phone}' => $order->customer->phone ?? '',
            '{order_date}' => $order->created_at ? $order->created_at->format('d/m/Y') : date('d/m/Y'),
            '{total_amount}' => $order->total_amount ?? '৳0',
            '{payment_method}' => $order->payment_method ?? 'ক্যাশ অন ডেলিভারি',
            '{delivery_address}' => $order->delivery_address ?? '',
        ];

        $message = str_replace(array_keys($replacements), array_values($replacements), $template);

        return strip_tags($message);
    }
}
