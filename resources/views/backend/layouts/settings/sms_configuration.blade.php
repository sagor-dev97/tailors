@extends('backend.app', ['title' => 'SMS Configuration'])

@section('content')
<!-- Font Awesome CDN (যদি না থাকে) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<!--app-content open-->
<div class="app-content main-content mt-0">
    <div class="side-app">

        <!-- CONTAINER -->
        <div class="main-container container-fluid">

            {{-- PAGE-HEADER --}}
            <div class="page-header">
                <div>
                    <h1 class="page-title" style="font-size: 24px; font-weight: 600; color: #1e293b;">SMS Configuration</h1>
                    <p style="color: #64748b; margin-top: 5px; font-size: 14px;">Configure SMS providers and settings</p>
                </div>
                <div class="ms-auto pageheader-btn">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);" style="color: #3b82f6;">Settings</a></li>
                        <li class="breadcrumb-item active" aria-current="page" style="color: #64748b;">SMS Configuration</li>
                    </ol>
                </div>
            </div>
            {{-- PAGE-HEADER --}}

            {{-- Change Provider Section --}}
            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px 20px; margin-bottom: 25px; display: flex; align-items: center; justify-content: space-between;">
                <span style="font-weight: 600; color: #1e293b; font-size: 16px;">Change Provider</span>
                <div style="display: flex; gap: 10px;">
                    <select style="border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 12px; width: 150px; color: #1e293b; background-color: white;">
                        <option selected>Mram</option>
                        <option>Twilio</option>
                        <option>Vonage</option>
                    </select>
                    <button style="background-color: #3b82f6; color: white; border: none; border-radius: 6px; padding: 8px 20px; font-weight: 500; cursor: pointer;">Update</button>
                </div>
            </div>

            {{-- Active Provider Info - Three Cards with Toggle Switches --}}
            <div style="display: flex; gap: 20px; margin-bottom: 30px;">
                {{-- Active Provider Card --}}
                <div style="flex: 2; background-color: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
                        <i class="fas fa-mobile-alt" style="color: #3b82f6; font-size: 18px;"></i>
                        <span style="font-weight: 500; color: #64748b; font-size: 14px;">Active Provider</span>
                    </div>
                    <div style="font-size: 24px; font-weight: 600; color: #1e293b;">Mram</div>
                </div>

                {{-- SMS Service Card with Toggle --}}
                <div style="flex: 1; background-color: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-envelope" style="color: #3b82f6; font-size: 16px;"></i>
                            <span style="font-weight: 500; color: #64748b; font-size: 14px;">SMS Service</span>
                        </div>
                        {{-- Toggle Switch --}}
                        <label class="switch" style="position: relative; display: inline-block; width: 46px; height: 24px;">
                            <input type="checkbox" id="smsServiceToggle" checked onchange="toggleSmsService(this)">
                            <span class="slider" style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #10b981; border-radius: 24px; transition: .3s;"></span>
                            <span class="toggle-knob" style="position: absolute; height: 20px; width: 20px; left: 2px; bottom: 2px; background-color: white; border-radius: 50%; transition: .3s; transform: translateX(22px);"></span>
                        </label>
                    </div>
                    <div id="smsServiceStatus" style="color: #10b981; font-weight: 600; font-size: 14px;">
                        <i class="fas fa-check-circle" style="margin-right: 5px;"></i> ACTIVE
                    </div>
                </div>

                {{-- Admission SMS Card with Toggle --}}
                <div style="flex: 1; background-color: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-graduation-cap" style="color: #3b82f6; font-size: 16px;"></i>
                            <span style="font-weight: 500; color: #64748b; font-size: 14px;">Admission SMS</span>
                        </div>
                        {{-- Toggle Switch --}}
                        <label class="switch" style="position: relative; display: inline-block; width: 46px; height: 24px;">
                            <input type="checkbox" id="admissionSmsToggle" checked onchange="toggleAdmissionSms(this)">
                            <span class="slider" style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #10b981; border-radius: 24px; transition: .3s;"></span>
                            <span class="toggle-knob" style="position: absolute; height: 20px; width: 20px; left: 2px; bottom: 2px; background-color: white; border-radius: 50%; transition: .3s; transform: translateX(22px);"></span>
                        </label>
                    </div>
                    <div id="admissionSmsStatus" style="color: #10b981; font-weight: 600; font-size: 14px;">
                        <i class="fas fa-check-circle" style="margin-right: 5px;"></i> ACTIVE
                    </div>
                </div>
            </div>

            {{-- SMS Provider Configuration --}}
            <div style="background-color: white; border: 1px solid #e2e8f0; border-radius: 8px;">
                {{-- Header --}}
                <div style="border-bottom: 1px solid #e2e8f0; padding: 20px;">
                    <h2 style="font-size: 20px; font-weight: 600; color: #1e293b; margin: 0;">
                        <i class="fas fa-cog" style="margin-right: 10px; color: #3b82f6;"></i>
                        SMS Provider Configuration
                    </h2>
                </div>

                {{-- Body --}}
                <div style="padding: 25px;">
                    {{-- Provider Name and Status with Icon --}}
                    <div style="margin-bottom: 25px;">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 5px;">
                            <i class="fas fa-server" style="color: #3b82f6; font-size: 20px;"></i>
                            <div style="font-size: 16px; font-weight: 600; color: #1e293b;">Mram SMS</div>
                        </div>
                        <div style="color: #10b981; font-weight: 500; margin-left: 30px;">
                            <i class="fas fa-circle" style="font-size: 8px; margin-right: 5px; vertical-align: middle;"></i> Active
                        </div>
                    </div>

                    {{-- API Key Field with Eye Toggle --}}
                    <div style="margin-bottom: 20px;">
                        <div style="font-weight: 500; color: #1e293b; margin-bottom: 8px;">
                            <i class="fas fa-key" style="margin-right: 8px; color: #64748b;"></i>API Key:
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <div style="flex: 1; position: relative;">
                                <input type="password" id="apiKeyInput" style="width: 100%; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 40px 10px 15px; background-color: #f8fafc;" placeholder="Enter API Key" value="1234567890abcdef">
                                <i class="fas fa-eye" id="toggleApiKey" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #64748b; cursor: pointer;"></i>
                            </div>
                            <button type="button" class="sync-btn" style="background-color: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 20px; color: #1e293b; cursor: pointer;">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Sender ID Field with Eye Toggle --}}
                    <div style="margin-bottom: 25px;">
                        <div style="font-weight: 500; color: #1e293b; margin-bottom: 8px;">
                            <i class="fas fa-id-card" style="margin-right: 8px; color: #64748b;"></i>Sender ID:
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <div style="flex: 1; position: relative;">
                                <input type="password" id="senderIdInput" style="width: 100%; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 40px 10px 15px; background-color: #f8fafc;" placeholder="Enter Sender ID" value="MRAM123">
                                <i class="fas fa-eye" id="toggleSenderId" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #64748b; cursor: pointer;"></i>
                            </div>
                            <button type="button" class="sync-btn" style="background-color: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 20px; color: #1e293b; cursor: pointer;">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Update Button with Icon --}}
                    <div>
                        <button style="background-color: #3b82f6; color: white; border: none; border-radius: 6px; padding: 12px 30px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                            <i class="fas fa-save"></i> Update Configuration
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- CONTAINER CLOSED -->

<style>
    /* Toggle Switch Styles */
    .switch {
        position: relative;
        display: inline-block;
        width: 46px;
        height: 24px;
    }
    
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .switch .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #10b981;
        border-radius: 24px;
        transition: .3s;
    }
    
    .switch input:not(:checked) + .slider {
        background-color: #cbd5e1;
    }
    
    .switch .toggle-knob {
        position: absolute;
        height: 20px;
        width: 20px;
        left: 2px;
        bottom: 2px;
        background-color: white;
        border-radius: 50%;
        transition: .3s;
    }
    
    .switch input:checked + .slider + .toggle-knob {
        transform: translateX(22px);
    }
    
    .switch input:not(:checked) + .slider + .toggle-knob {
        transform: translateX(2px);
    }
    
    /* Hover Effects */
    .switch:hover .slider {
        opacity: 0.9;
    }
    
    .sync-btn:hover {
        background-color: #e2e8f0 !important;
    }
    
    button:hover {
        opacity: 0.9;
    }
</style>

@endsection

@push('scripts')
<script>
    // API Key Toggle
    document.getElementById('toggleApiKey')?.addEventListener('click', function() {
        const input = document.getElementById('apiKeyInput');
        const icon = this;
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });

    // Sender ID Toggle
    document.getElementById('toggleSenderId')?.addEventListener('click', function() {
        const input = document.getElementById('senderIdInput');
        const icon = this;
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });

    // SMS Service Toggle Function
    window.toggleSmsService = function(checkbox) {
        const statusDiv = document.getElementById('smsServiceStatus');
        const slider = checkbox.nextElementSibling;
        const knob = slider.nextElementSibling;
        
        if (checkbox.checked) {
            statusDiv.innerHTML = '<i class="fas fa-check-circle" style="margin-right: 5px;"></i> ACTIVE';
            statusDiv.style.color = '#10b981';
            slider.style.backgroundColor = '#10b981';
        } else {
            statusDiv.innerHTML = '<i class="fas fa-times-circle" style="margin-right: 5px;"></i> INACTIVE';
            statusDiv.style.color = '#ef4444';
            slider.style.backgroundColor = '#cbd5e1';
        }
        
        // Ajax call example (uncomment to use)
        /*
        fetch('/admin/settings/sms/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                type: 'sms_service',
                status: checkbox.checked ? 1 : 0
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // show success message
            }
        });
        */
    };

    // Admission SMS Toggle Function
    window.toggleAdmissionSms = function(checkbox) {
        const statusDiv = document.getElementById('admissionSmsStatus');
        const slider = checkbox.nextElementSibling;
        const knob = slider.nextElementSibling;
        
        if (checkbox.checked) {
            statusDiv.innerHTML = '<i class="fas fa-check-circle" style="margin-right: 5px;"></i> ACTIVE';
            statusDiv.style.color = '#10b981';
            slider.style.backgroundColor = '#10b981';
        } else {
            statusDiv.innerHTML = '<i class="fas fa-times-circle" style="margin-right: 5px;"></i> INACTIVE';
            statusDiv.style.color = '#ef4444';
            slider.style.backgroundColor = '#cbd5e1';
        }
        
        // Ajax call example (uncomment to use)
        /*
        fetch('/admin/settings/sms/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                type: 'admission_sms',
                status: checkbox.checked ? 1 : 0
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // show success message
            }
        });
        */
    };

    // Sync/Refresh button functionality
    document.querySelectorAll('.sync-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const icon = this.querySelector('i');
            icon.classList.add('fa-spin');
            
            // Simulate API call
            setTimeout(() => {
                icon.classList.remove('fa-spin');
                // Show success message (you can use toast or alert)
                alert('API key refreshed successfully!');
            }, 1000);
        });
    });
</script>
@endpush