@extends('backend.app', ['title' => 'SMS Configuration'])

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <div class="app-content main-content mt-0">
        <div class="side-app">
            <div class="main-container container-fluid">

                {{-- PAGE HEADER --}}
                <div class="page-header">
                    <div>
                        <h1 class="page-title">SMS Configuration</h1>
                        <p style="color:#64748b;">Configure SMS providers, templates and dynamic variables</p>
                    </div>
                </div>

                {{-- TOP CARD --}}
                <div style="display:flex;gap:20px;margin-bottom:25px;">
                    <div style="flex:2;background:#fff;border:1px solid #e2e8f0;padding:20px;border-radius:8px;">
                        <div style="font-size:14px;color:#64748b;">Active Provider</div>
                        <div style="font-size:22px;font-weight:700;">Automas</div>
                    </div>

                    <div style="flex:1;background:#fff;border:1px solid #e2e8f0;padding:20px;border-radius:8px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span>SMS Service</span>
                            <label class="switch">
                                <input type="checkbox" id="smsServiceToggle"
                                    {{ isset($smsSetting) && $smsSetting->service_status == 1 ? 'checked' : '' }}
                                    onchange="toggleSmsService(this)">
                                <span class="slider"></span>
                                <span class="toggle-knob"></span>
                            </label>
                        </div>
                        <div id="smsServiceStatus"
                            style="margin-top:15px;font-weight:600;
                        color:{{ isset($smsSetting) && $smsSetting->service_status ? '#10b981' : '#ef4444' }};">
                            @if (isset($smsSetting) && $smsSetting->service_status)
                                <i class="fas fa-check-circle"></i> ACTIVE
                            @else
                                <i class="fas fa-times-circle"></i> INACTIVE
                            @endif
                        </div>
                    </div>
                </div>

                {{-- MAIN CONFIG BOX --}}
                <div style="background:#fff;border:1px solid #e2e8f0;border-radius:8px;">
                    <div style="padding:20px;border-bottom:1px solid #e2e8f0;">
                        <h3>SMS Provider Configuration</h3>
                    </div>

                    <div style="padding:25px;">
                        {{-- API KEY --}}
                        <div style="margin-bottom:20px;">
                            <label>API Key</label>
                            <div style="position:relative;">
                                <input type="password" id="apiKeyInput" class="form-control"
                                    value="{{ $smsSetting->api_key ?? '' }}">
                                <i class="fas fa-eye" id="toggleApiKey"
                                    style="position:absolute;right:12px;top:12px;cursor:pointer;color:#64748b;"></i>
                            </div>
                        </div>

                        {{-- SENDER ID --}}
                        <div style="margin-bottom:20px;">
                            <label>Sender ID</label>
                            <div style="position:relative;">
                                <input type="password" id="senderIdInput" class="form-control"
                                    value="{{ $smsSetting->sender_id ?? '' }}">
                                <i class="fas fa-eye" id="toggleSenderId"
                                    style="position:absolute;right:12px;top:12px;cursor:pointer;color:#64748b;"></i>
                            </div>
                        </div>

                        {{-- DYNAMIC TEMPLATE SECTION --}}
                        {{-- <div
                            style="margin-top:25px;padding:20px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc;">
                            <h4 style="margin-bottom:20px;">📱 SMS Template Configuration</h4>

                            {{-- SENDER --}}
                            <div style="margin-top:10px;">
                                <label>Sender Name</label>
                                <input type="text" id="senderInput" class="form-control mt-2"
                                    placeholder="e.g., Ecommerce BD" value="{{ $smsSetting->sender ?? '' }}">
                            </div>

                            {{-- TYPE --}}
                            <div style="margin-top:15px;"> 
                                <label>Message Type</label>
                                <select id="typeInput" class="form-control mt-2">
                                    <option value="text"
                                        {{ isset($smsSetting) && $smsSetting->type == 'text' ? 'selected' : '' }}>Text
                                        Message</option>
                                    <option value="unicode"
                                        {{ isset($smsSetting) && $smsSetting->type == 'unicode' ? 'selected' : '' }}>Unicode
                                        (বাংলা)</option>
                                </select>
                            </div>

                            {{-- AVAILABLE TAGS - DYNAMIC --}}
                            <div
                                style="margin-top:20px;padding:15px;background:#fff;border-radius:8px;border:1px solid #e2e8f0;">
                                <label style="font-weight:600;margin-bottom:10px;">🔖 Available Variables (Tags):</label>
                                <div style="display:flex;flex-wrap:wrap;gap:10px;" id="tagList">
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="insertTag('order_number')">
                                        <code>{order_number}</code> - Order Number
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="insertTag('customer_name')">
                                        <code>{customer_name}</code> - Customer Name
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="insertTag('customer_phone')">
                                        <code>{customer_phone}</code> - Customer Phone
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="insertTag('status')">
                                        <code>{status}</code> - Status (English)
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="insertTag('bangla_status')">
                                        <code>{bangla_status}</code> - Status (বাংলা)
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="insertTag('company')">
                                        <code>{company}</code> - Company Name
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="insertTag('order_date')">
                                        <code>{order_date}</code> - Order Date
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="insertTag('total_amount')">
                                        <code>{total_amount}</code> - Total Amount
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="insertTag('payment_method')">
                                        <code>{payment_method}</code> - Payment Method
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="insertTag('delivery_address')">
                                        <code>{delivery_address}</code> - Delivery Address
                                    </button>
                                </div>
                                <small class="text-muted" style="display:block;margin-top:10px;">
                                    💡 Click on any tag to insert it into the message template
                                </small>
                            </div>

                            {{-- TEMPLATE FOR DIFFERENT STATUSES --}}
                            <div style="margin-top:20px;">
                                <ul class="nav nav-tabs" id="templateTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#pending">Pending</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#processing">Processing</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#shipped">Shipped</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#delivered">Delivered</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#completed">Completed</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#cancelled">Cancelled</a>
                                    </li>

                                    {{-- নতুন টেমপ্লেট যোগ করার বাটন --}}
                                    <li class="nav-item">
                                        <button type="button" class="btn btn-sm btn-success ml-2"
                                            onclick="showAddTemplateModal()" style="margin-left: 10px;">
                                            <i class="fas fa-plus"></i> Add New Status
                                        </button>
                                    </li>
                                </ul>

                                @php
                                    $templates = $smsSetting->templates_json ?? [];
                                @endphp

                                <div class="tab-content" style="margin-top:20px;">
                                    {{-- PENDING --}}
                                    <div class="tab-pane active" id="pending">
                                        <label>Pending Status Message Template</label>
                                        <textarea id="pendingTemplate" class="form-control template-editor" rows="4"
                                            placeholder="Write message for pending status...">{{ $templates['pending'] ?? 'প্রিয় {customer_name}, আপনার অর্ডার নং {order_number} প্রক্রিয়াকরণের অপেক্ষায় আছে। ধন্যবাদ {company}' }}</textarea>
                                        <button type="button" class="btn btn-sm btn-info mt-2"
                                            onclick="previewMessage('pending')">
                                            <i class="fas fa-eye"></i> Preview Message
                                        </button>
                                    </div>

                                    {{-- PROCESSING --}}
                                    <div class="tab-pane fade" id="processing">
                                        <label>Processing Status Message Template</label>
                                        <textarea id="processingTemplate" class="form-control template-editor" rows="4"
                                            placeholder="Write message for processing status...">{{ $templates['processing'] ?? 'প্রিয় {customer_name}, আপনার অর্ডার নং {order_number} প্রক্রিয়াধীন। শীঘ্রই পাঠানো হবে। ধন্যবাদ {company}' }}</textarea>
                                        <button type="button" class="btn btn-sm btn-info mt-2"
                                            onclick="previewMessage('processing')">
                                            <i class="fas fa-eye"></i> Preview Message
                                        </button>
                                    </div>

                                    {{-- SHIPPED --}}
                                    <div class="tab-pane fade" id="shipped">
                                        <label>Shipped Status Message Template</label>
                                        <textarea id="shippedTemplate" class="form-control template-editor" rows="4"
                                            placeholder="Write message for shipped status...">{{ $templates['shipped'] ?? 'প্রিয় {customer_name}, আপনার অর্ডার নং {order_number} পাঠানো হয়েছে। ধন্যবাদ {company}' }}</textarea>
                                        <button type="button" class="btn btn-sm btn-info mt-2"
                                            onclick="previewMessage('shipped')">
                                            <i class="fas fa-eye"></i> Preview Message
                                        </button>
                                    </div>

                                    {{-- DELIVERED --}}
                                    <div class="tab-pane fade" id="delivered">
                                        <label>Delivered Status Message Template</label>
                                        <textarea id="deliveredTemplate" class="form-control template-editor" rows="4"
                                            placeholder="Write message for delivered status...">{{ $templates['delivered'] ?? 'প্রিয় {customer_name}, আপনার অর্ডার নং {order_number} ডেলিভারি হয়েছে। আমাদের সাথে থাকার জন্য ধন্যবাদ। {company}' }}</textarea>
                                        <button type="button" class="btn btn-sm btn-info mt-2"
                                            onclick="previewMessage('delivered')">
                                            <i class="fas fa-eye"></i> Preview Message
                                        </button>
                                    </div>

                                    {{-- COMPLETED --}}
                                    <div class="tab-pane fade" id="completed">
                                        <label>Completed Status Message Template</label>
                                        <textarea id="completedTemplate" class="form-control template-editor" rows="4"
                                            placeholder="Write message for completed status...">{{ $templates['completed'] ?? 'প্রিয় {customer_name}, আপনার অর্ডার নং {order_number} সম্পন্ন হয়েছে। আপনার মূল্যবান মতামত জানান। {company}' }}</textarea>
                                        <button type="button" class="btn btn-sm btn-info mt-2"
                                            onclick="previewMessage('completed')">
                                            <i class="fas fa-eye"></i> Preview Message
                                        </button>
                                    </div>

                                    {{-- CANCELLED --}}
                                    <div class="tab-pane fade" id="cancelled">
                                        <label>Cancelled Status Message Template</label>
                                        <textarea id="cancelledTemplate" class="form-control template-editor" rows="4"
                                            placeholder="Write message for cancelled status...">{{ $templates['cancelled'] ?? 'প্রিয় {customer_name}, আপনার অর্ডার নং {order_number} বাতিল করা হয়েছে। কোনো টাকা কাটা হলে联系我们। {company}' }}</textarea>
                                        <button type="button" class="btn btn-sm btn-info mt-2"
                                            onclick="previewMessage('cancelled')">
                                            <i class="fas fa-eye"></i> Preview Message
                                        </button>
                                    </div>

                                    {{-- ডায়নামিক টেমপ্লেট গুলো এখানে লোড হবে --}}
                                    <div id="dynamicTemplatesContainer">
                                        @foreach ($templates as $status => $template)
                                            @if (!in_array($status, ['pending', 'processing', 'shipped', 'delivered', 'completed', 'cancelled', 'default']))
                                                <div class="tab-pane fade" id="dynamic_{{ $status }}">
                                                    <label>{{ ucfirst(str_replace('_', ' ', $status)) }} Status Message
                                                        Template</label>
                                                    <textarea id="dynamicTemplate_{{ $status }}" class="form-control template-editor" rows="4"
                                                        placeholder="Write message for {{ $status }} status...">{{ $template }}</textarea>
                                                    <button type="button" class="btn btn-sm btn-info mt-2"
                                                        onclick="previewMessage('{{ $status }}')">
                                                        <i class="fas fa-eye"></i> Preview Message
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger mt-2 ml-2"
                                                        onclick="deleteTemplate('{{ $status }}')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- DEFAULT TEMPLATE (Fallback) --}}
                            <div style="margin-top:20px;padding:15px;background:#fff3cd;border-radius:8px;">
                                <label>Default Template (Fallback)</label>
                                <textarea id="defaultTemplate" class="form-control" rows="3"
                                    placeholder="Default template if status specific not found">{{ $templates['default'] ?? 'আপনার অর্ডার {order_number} স্ট্যাটাস {bangla_status} এ আপডেট হয়েছে। ধন্যবাদ {company}' }}</textarea>
                                <small class="text-muted">⚠️ This template will be used if no status-specific template is
                                    found</small>
                            </div>
                        </div> --}}

                        {{-- STATUS LABELS (Bangla names for statuses) --}}
                        <div
                            style="margin-top:20px;padding:15px;background:#fff;border-radius:8px;border:1px solid #e2e8f0;">
                            <label style="font-weight:600;margin-bottom:10px;display:block;">🏷️ Status Labels
                                (বাংলা)</label>
                            @php
                                $statusLabels = $smsSetting->status_labels ?? [];
                                $allStatuses = [
                                    'pending',
                                    'processing',
                                    'completed',
                                    'canceled',
                                    'shipped',
                                    'delivered',
                                    'ready',
                                    'returned',
                                    'failed',
                                    'in_courier',
                                    'courier_payment_not',
                                    'payment_not',
                                ];
                            @endphp
                            <div style="display:flex;flex-wrap:wrap;gap:10px;">
                                @foreach ($allStatuses as $s)
                                    <div style="flex:0 0 48%;">
                                        <label style="font-size:13px;margin-bottom:4px;">{{ $s }}</label>
                                        <input type="text" class="form-control status-label-input"
                                            data-status="{{ $s }}" value="{{ $statusLabels[$s] ?? '' }}"
                                            placeholder="{{ $s }} এর বাংলা নাম লিখুন">
                                    </div>
                                @endforeach
                            </div>
                            <small class="text-muted">প্রতি স্ট্যাটাসের জন্য বাংলা লেবেল লিখুন — এগুলো টেমপ্লেটে
                                `{bangla_status}` দ্বারা ইনসার্ট করা হবে।</small>
                        </div>

                        {{-- Add New Template Modal --}}
                        <div class="modal fade" id="addTemplateModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">➕ Add New Status Template</h5>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>Status Name <span class="text-danger">*</span></label>
                                            <input type="text" id="newStatusName" class="form-control"
                                                placeholder="e.g., on_hold, returned, ready_to_ship">
                                            <small class="text-muted">Use lowercase and underscore (_) instead of
                                                space</small>
                                        </div>
                                        <div class="form-group">
                                            <label>Message Template <span class="text-danger">*</span></label>
                                            <textarea id="newTemplateMessage" class="form-control" rows="5"
                                                placeholder="Write your template message here..."></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Available Tags:</label>
                                            <div>
                                                <button type="button" class="btn btn-sm btn-outline-secondary mb-1"
                                                    onclick="insertTagInModal('order_number')">
                                                    {order_number}
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary mb-1"
                                                    onclick="insertTagInModal('customer_name')">
                                                    {customer_name}
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary mb-1"
                                                    onclick="insertTagInModal('customer_phone')">
                                                    {customer_phone}
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary mb-1"
                                                    onclick="insertTagInModal('status')">
                                                    {status}
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary mb-1"
                                                    onclick="insertTagInModal('bangla_status')">
                                                    {bangla_status}
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary mb-1"
                                                    onclick="insertTagInModal('company')">
                                                    {company}
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary mb-1"
                                                    onclick="insertTagInModal('order_date')">
                                                    {order_date}
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary mb-1"
                                                    onclick="insertTagInModal('total_amount')">
                                                    {total_amount}
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary mb-1"
                                                    onclick="insertTagInModal('payment_method')">
                                                    {payment_method}
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary mb-1"
                                                    onclick="insertTagInModal('delivery_address')">
                                                    {delivery_address}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Cancel</button>
                                        <button type="button" class="btn btn-primary" onclick="addNewTemplate()">Add
                                            Template</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- PREVIEW MODAL --}}
                        <div class="modal fade" id="previewModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">📱 SMS Preview</h5>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                        <div
                                            style="background:#e5e5ea;padding:20px;border-radius:10px;margin-bottom:15px;">
                                            <div style="background:#fff;padding:15px;border-radius:10px;">
                                                <div style="font-size:14px;color:#8e8e93;margin-bottom:5px;">Message
                                                    Preview</div>
                                                <div id="previewMessage"
                                                    style="font-size:16px;line-height:1.5;word-wrap:break-word;"></div>
                                            </div>
                                        </div>
                                        <div class="alert alert-info">
                                            <strong>ℹ️ Sample Data Used:</strong><br>
                                            Order Number: <code>ORD-123456</code><br>
                                            Customer: <code>রহিম উদ্দিন</code><br>
                                            Amount: <code>৳1,250</code>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary" onclick="copyToClipboard()">
                                            <i class="fas fa-copy"></i> Copy Message
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- UPDATE BUTTON --}}
                        <button id="updateSmsConfig" class="btn btn-primary mt-3">
                            <i class="fas fa-save"></i> Update Configuration
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- STYLES --}}
<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 46px;
        height: 24px;
    }

    .switch input {
        display: none;
    }

    .slider {
        position: absolute;
        inset: 0;
        background: #cbd5e1;
        border-radius: 24px;
        transition: .3s;
    }

    .toggle-knob {
        position: absolute;
        width: 20px;
        height: 20px;
        left: 2px;
        bottom: 2px;
        background: #fff;
        border-radius: 50%;
        transition: .3s;
    }

    .switch input:checked+.slider {
        background: #10b981;
    }

    .switch input:checked+.slider+.toggle-knob {
        transform: translateX(22px);
    }

    .nav-tabs .nav-link {
        color: #475569;
        font-weight: 500;
    }

    .nav-tabs .nav-link.active {
        color: #3b82f6;
        border-bottom: 2px solid #3b82f6;
    }

    .template-editor {
        font-family: monospace;
        font-size: 14px;
    }

    .btn-outline-primary:hover code {
        color: white;
    }

    code {
        background: #f1f5f9;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 12px;
    }
</style>

{{-- SCRIPTS --}}
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // Tag insertion function
        function insertTag(tag) {
            let activeTab = $('.nav-tabs .nav-link.active').attr('href');
            let textareaId = '';

            if (activeTab === '#pending') textareaId = 'pendingTemplate';
            else if (activeTab === '#processing') textareaId = 'processingTemplate';
            else if (activeTab === '#shipped') textareaId = 'shippedTemplate';
            else if (activeTab === '#delivered') textareaId = 'deliveredTemplate';
            else if (activeTab === '#completed') textareaId = 'completedTemplate';
            else if (activeTab === '#cancelled') textareaId = 'cancelledTemplate';
            else if (activeTab && activeTab.startsWith('#dynamic_')) {
                let dynamicId = activeTab.replace('#dynamic_', '');
                textareaId = 'dynamicTemplate_' + dynamicId;
            }

            let textarea = document.getElementById(textareaId);
            if (textarea) {
                let start = textarea.selectionStart;
                let end = textarea.selectionEnd;
                let text = textarea.value;
                let before = text.substring(0, start);
                let after = text.substring(end, text.length);
                textarea.value = before + '{' + tag + '}' + after;
                textarea.focus();
                textarea.setSelectionRange(start + tag.length + 2, start + tag.length + 2);
            }
        }

        // Preview message function
        function previewMessage(status) {
            let template = '';

            // চেক করা ডায়নামিক টেমপ্লেট কিনা
            if ($(`#dynamicTemplate_${status}`).length > 0) {
                template = $(`#dynamicTemplate_${status}`).val();
            } else {
                switch (status) {
                    case 'pending':
                        template = $('#pendingTemplate').val();
                        break;
                    case 'processing':
                        template = $('#processingTemplate').val();
                        break;
                    case 'shipped':
                        template = $('#shippedTemplate').val();
                        break;
                    case 'delivered':
                        template = $('#deliveredTemplate').val();
                        break;
                    case 'completed':
                        template = $('#completedTemplate').val();
                        break;
                    case 'cancelled':
                        template = $('#cancelledTemplate').val();
                        break;
                    default:
                        template = $('#defaultTemplate').val();
                }
            }

            // Sample data for preview
            const sampleData = {
                order_number: 'ORD-123456',
                customer_name: 'রহিম উদ্দিন',
                customer_phone: '017XXXXXXXX',
                status: status,
                bangla_status: getBanglaStatus(status),
                company: $('#senderInput').val() || 'ই-কমার্স বিডি',
                order_date: new Date().toLocaleDateString('bn-BD'),
                total_amount: '৳1,250',
                payment_method: 'ক্যাশ অন ডেলিভারি',
                delivery_address: 'ঢাকা, বাংলাদেশ'
            };

            let message = template;
            for (let [key, value] of Object.entries(sampleData)) {
                let regex = new RegExp(`\\{${key}\\}`, 'g');
                message = message.replace(regex, value);
            }

            $('#previewMessage').html(message.replace(/\n/g, '<br>'));
            $('#previewModal').modal('show');
        }

        function getBanglaStatus(status) {
            // prefer admin entered labels
            let input = $(`.status-label-input[data-status='${status}']`);
            if (input.length && input.val().trim() !== '') return input.val().trim();

            const statuses = {
                pending: 'অপেক্ষমান',
                processing: 'প্রক্রিয়াধীন',
                shipped: 'পাঠানো হয়েছে',
                delivered: 'ডেলিভারি হয়েছে',
                completed: 'সম্পন্ন হয়েছে',
                cancelled: 'বাতিল করা হয়েছে'
            };
            return statuses[status] || status;
        }

        function copyToClipboard() {
            let text = $('#previewMessage').text();
            navigator.clipboard.writeText(text).then(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Copied!',
                    text: 'Message copied to clipboard',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        }

        // Password hide/show
        $('#toggleApiKey').click(function() {
            let input = $('#apiKeyInput');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                $(this).removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                $(this).removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        $('#toggleSenderId').click(function() {
            let input = $('#senderIdInput');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                $(this).removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                $(this).removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // ============= নতুন টেমপ্লেট যোগ করার ফাংশন =============

        // মডাল দেখানো
        function showAddTemplateModal() {
            $('#newStatusName').val('');
            $('#newTemplateMessage').val('');
            $('#addTemplateModal').modal('show');
        }

        // মডালে ট্যাগ ইনসার্ট
        function insertTagInModal(tag) {
            let textarea = document.getElementById('newTemplateMessage');
            if (textarea) {
                let start = textarea.selectionStart;
                let end = textarea.selectionEnd;
                let text = textarea.value;
                let before = text.substring(0, start);
                let after = text.substring(end, text.length);
                textarea.value = before + '{' + tag + '}' + after;
                textarea.focus();
                textarea.setSelectionRange(start + tag.length + 2, start + tag.length + 2);
            }
        }

        // নতুন টেমপ্লেট যোগ করা (UI তে)
        function addNewTemplate() {
            let statusName = $('#newStatusName').val().trim().toLowerCase();
            let templateMessage = $('#newTemplateMessage').val().trim();

            if (!statusName) {
                Swal.fire('Error!', 'Please enter status name', 'error');
                return;
            }

            if (!templateMessage) {
                Swal.fire('Error!', 'Please enter template message', 'error');
                return;
            }

            // স্পেসকে underscore এ রূপান্তর
            statusName = statusName.replace(/\s+/g, '_');

            // চেক করা আগে থেকে আছে কিনা
            let existingTab = $(`#dynamic_${statusName}`);
            if (existingTab.length > 0) {
                Swal.fire('Error!', 'This status already exists', 'error');
                return;
            }

            // চেক করা ডিফল্ট স্ট্যাটাসের সাথে কনফ্লিক্ট কিনা
            let defaultStatuses = ['pending', 'processing', 'shipped', 'delivered', 'completed', 'cancelled', 'default'];
            if (defaultStatuses.includes(statusName)) {
                Swal.fire('Error!', `${statusName} is a default status. Please use another name.`, 'error');
                return;
            }

            // নতুন ট্যাব যোগ করা
            let displayName = statusName.replace(/_/g, ' ').toUpperCase();
            let newTab = `
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#dynamic_${statusName}">
                ${displayName}
            </a>
        </li>
    `;

            // Add new tab before the "Add New" button
            $('#templateTab li.nav-item:last').before(newTab);

            // নতুন ট্যাব কন্টেন্ট
            let newContent = `
        <div class="tab-pane fade" id="dynamic_${statusName}">
            <label>${displayName} Status Message Template</label>
            <textarea id="dynamicTemplate_${statusName}" class="form-control template-editor" rows="4"
                placeholder="Write message for ${statusName} status...">${escapeHtml(templateMessage)}</textarea>
            <button type="button" class="btn btn-sm btn-info mt-2" onclick="previewMessage('${statusName}')">
                <i class="fas fa-eye"></i> Preview Message
            </button>
            <button type="button" class="btn btn-sm btn-danger mt-2 ml-2" onclick="deleteTemplate('${statusName}')">
                <i class="fas fa-trash"></i> Delete
            </button>
        </div>
    `;

            $('#dynamicTemplatesContainer').append(newContent);

            // মডাল বন্ধ করা
            $('#addTemplateModal').modal('hide');

            Swal.fire('Success!', 'New template added successfully. Click Save to store permanently.', 'success');
        }

        // টেমপ্লেট ডিলিট করা (UI থেকে)
        function deleteTemplate(statusName) {
            Swal.fire({
                title: 'Are you sure?',
                text: `You want to delete "${statusName}" template?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // ট্যাব রিমুভ
                    $(`a[href="#dynamic_${statusName}"]`).parent().remove();
                    $(`#dynamic_${statusName}`).remove();

                    Swal.fire('Deleted!', 'Template deleted from UI. Click Save to update database.', 'success');
                }
            });
        }

        // HTML escape function
        function escapeHtml(text) {
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // ============= আপডেট ফাংশন =============

        // Update configuration
        $(document).ready(function() {
            $('#updateSmsConfig').click(function() {
                let btn = $(this);
                btn.html('<i class="fas fa-spinner fa-spin"></i> Saving...');

                // বেসিক টেমপ্লেট গুলো array তে নিন
                // collect and normalize templates (allow admins to paste PHP-style templates)
                function normalizeTemplate(tpl) {
                    if (!tpl || typeof tpl !== 'string') return tpl;
                    tpl = tpl.replace(/\{\$[^}]*->([a-zA-Z0-9_]+)\}/g, '{$1}');
                    tpl = tpl.replace(/\{\$([a-zA-Z0-9_]+)\}/g, '{$1}');
                    return tpl;
                }

                let templates = {
                    pending: normalizeTemplate($('#pendingTemplate').val()),
                    processing: normalizeTemplate($('#processingTemplate').val()),
                    shipped: normalizeTemplate($('#shippedTemplate').val()),
                    delivered: normalizeTemplate($('#deliveredTemplate').val()),
                    completed: normalizeTemplate($('#completedTemplate').val()),
                    cancelled: normalizeTemplate($('#cancelledTemplate').val()),
                    default: normalizeTemplate($('#defaultTemplate').val())
                };

                // ডায়নামিক টেমপ্লেট গুলো যোগ করুন
                $('[id^="dynamicTemplate_"]').each(function() {
                    let id = $(this).attr('id');
                    let statusName = id.replace('dynamicTemplate_', '');
                    templates[statusName] = normalizeTemplate($(this).val());
                });

                let formData = {
                    _token: "{{ csrf_token() }}",
                    api_key: $('#apiKeyInput').val(),
                    sender_id: $('#senderIdInput').val(),
                    sender: $('#senderInput').val(),
                    type: $('#typeInput').val(),
                    sms_format: 'json',
                    templates_json: templates,
                    status_labels: (function() {
                        let obj = {};
                        $('.status-label-input').each(function() {
                            let k = $(this).data('status');
                            let v = $(this).val();
                            if (v && v.trim() !== '') obj[k] = v.trim();
                        });
                        return obj;
                    })()
                };

                $.ajax({
                    url: "{{ route('admin.setting.sms.update') }}",
                    type: "POST",
                    data: formData,
                    success: function(res) {
                        Swal.fire('Success!', res.message, 'success');
                        btn.html('<i class="fas fa-save"></i> Update Configuration');
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', xhr.responseJSON?.message || 'Something went wrong',
                            'error');
                        btn.html('<i class="fas fa-save"></i> Update Configuration');
                    }
                });
            });
        });

        // Toggle SMS service
        function toggleSmsService(el) {
            let status = el.checked ? 1 : 0;

            $.post("{{ route('admin.setting.sms.service.toggle') }}", {
                _token: "{{ csrf_token() }}",
                status: status
            }, function(res) {
                if (status) {
                    $('#smsServiceStatus')
                        .html('<i class="fas fa-check-circle"></i> ACTIVE')
                        .css('color', '#10b981');
                } else {
                    $('#smsServiceStatus')
                        .html('<i class="fas fa-times-circle"></i> INACTIVE')
                        .css('color', '#ef4444');
                }

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: res.message,
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        }
    </script>
@endpush
