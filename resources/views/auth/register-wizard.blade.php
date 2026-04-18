@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[#020617] relative overflow-hidden py-12" x-data="registrationWizard()">

    <!-- Background Elements -->
    <div class="absolute inset-0 z-0 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-blue-600/20 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[500px] h-[500px] bg-indigo-600/20 rounded-full blur-[120px]"></div>
    </div>

    <div class="w-full max-w-4xl p-8 relative z-10">
        <!-- Header -->
        <div class="text-center mb-10">
            <div class="flex justify-center mb-6">
                <img src="{{ asset('images/image.png') }}" alt="Logo" class="h-12 w-auto">
            </div>
            <h2 class="text-3xl font-black text-white mb-2 tracking-tight">Create Your Command Center</h2>
            <p class="text-slate-400">Join the next generation of fleet intelligence.</p>
        </div>

        <!-- Progress Indicator -->
        <div class="mb-12 relative">
            <div class="overflow-hidden h-1 mb-4 text-xs flex rounded-full bg-slate-800">
                <div :style="`width: ${(step / 4) * 100}%`" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500 transition-all duration-500"></div>
            </div>
            <div class="flex justify-between w-full text-xs font-bold text-slate-500 uppercase tracking-widest px-2">
                <span :class="step >= 1 ? 'text-blue-400' : ''">1. Company</span>
                <span :class="step >= 2 ? 'text-blue-400' : ''">2. Owner</span>
                <span :class="step >= 3 ? 'text-blue-400' : ''">3. Plan</span>
                <span :class="step >= 4 ? 'text-blue-400' : ''">4. Legal</span>
            </div>
        </div>

        <!-- Main Form Card -->
        <div class="bg-[#0f172a]/60 backdrop-blur-xl border border-white/5 rounded-3xl p-8 md:p-12 shadow-2xl relative overflow-hidden">

            <form @submit.prevent="submitForm">

                <!-- STEP 1: Company Info -->
                <div id="step-1" x-show="step === 1" x-transition.opacity.duration.300ms>
                    <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full bg-blue-500/20 text-blue-400 flex items-center justify-center text-sm">1</span>
                        Company Entity Details
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Company Legal Name</label>
                            <input type="text" x-model="form.company_name" required class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" placeholder="e.g. Apex Fleet Services LLC">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Trade License Number</label>
                            <input type="text" x-model="form.trade_license_number" required class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" placeholder="License No.">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">VAT / TRN Number</label>
                            <input type="text" x-model="form.trn_number" class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" placeholder="Optional">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Headquarters Address</label>
                            <input type="text" x-model="form.address" required class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" placeholder="Full Address">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Country</label>
                            <select x-model="form.country" required class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all appearance-none">
                                <option value="" disabled selected>Select Country</option>
                                <option value="United Arab Emirates">United Arab Emirates</option>
                                <option value="Saudi Arabia">Saudi Arabia</option>
                                <option value="Qatar">Qatar</option>
                                <option value="Oman">Oman</option>
                                <option value="Bahrain">Bahrain</option>
                                <option value="Kuwait">Kuwait</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Total Fleet Size</label>
                            <input type="number" x-model="form.total_vehicles" min="1" required class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" placeholder="Number of vehicles">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Contact Phone</label>
                            <input type="text" x-model="form.contact_phone" required class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" placeholder="+971 50 000 0000">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">How did you hear about us?</label>
                            <select x-model="form.registration_source" class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all appearance-none">
                                <option value="direct">Direct Website</option>
                                <option value="referral">Referral / Word of Mouth</option>
                                <option value="sales_team">Sales Team</option>
                                <option value="social">Social Media</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button type="button" @click="nextStep()" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-blue-900/20 transition-all">Next: Account Owner &rarr;</button>
                    </div>
                </div>

                <!-- STEP 2: Account Owner -->
                <div id="step-2" x-show="step === 2" style="display: none;" x-transition.opacity.duration.300ms>
                    <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full bg-purple-500/20 text-purple-400 flex items-center justify-center text-sm">2</span>
                        Master Account Profile
                    </h3>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Full Name</label>
                            <input type="text" x-model="form.owner_name" required class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Work Email (Login ID)</label>
                            <input type="email" x-model="form.owner_email" required class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Secure Password</label>
                                <input type="password" x-model="form.owner_password" required minlength="8" class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Confirm Password</label>
                                <input type="password" x-model="form.owner_password_confirmation" required minlength="8" class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                                <p x-show="form.owner_password !== form.owner_password_confirmation && form.owner_password_confirmation !== ''" class="text-rose-500 text-xs mt-2 uppercase tracking-tight font-bold">Passwords do not match</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-between">
                        <button type="button" @click="step--" class="text-slate-400 hover:text-white font-bold py-3 px-6 transition-all">&larr; Back</button>
                        <button type="button" @click="nextStep()" class="bg-purple-600 hover:bg-purple-500 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-purple-900/20 transition-all" :disabled="form.owner_password !== form.owner_password_confirmation">Next: Choose Plan &rarr;</button>
                    </div>
                </div>

                <!-- STEP 3: Plan Selection -->
                <div id="step-3" x-show="step === 3" style="display: none;" x-transition.opacity.duration.300ms>
                    <h3 class="text-xl font-bold text-white mb-2 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center text-sm">3</span>
                        Select Infrastructure Plan
                    </h3>
                    <p class="text-slate-400 text-sm mb-8 ml-11">Choose the engine that perfectly scales with your fleet.</p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Starter -->
                        <div @click="selectPlan('starter')" :class="form.plan === 'starter' ? 'border-amber-500 bg-amber-500/10' : 'border-slate-700 bg-slate-900/50 hover:border-slate-500'" class="cursor-pointer border rounded-2xl p-6 transition-all relative">
                            <h4 class="text-lg font-black text-white uppercase tracking-wider mb-1">Starter</h4>
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mb-4">Up to 10 Vehicles</p>
                            <div class="text-3xl font-black text-white mb-6">$99<span class="text-sm text-slate-500 font-normal">/mo</span></div>
                            <ul class="space-y-3 text-sm text-slate-300">
                                <li class="flex items-center gap-2"><span class="text-emerald-500">✔</span> Base Telematics</li>
                                <li class="flex items-center gap-2"><span class="text-emerald-500">✔</span> Standard Reports</li>
                                <li class="flex items-center gap-2 text-slate-600"><span class="text-rose-500">✖</span> AI Intelligence</li>
                            </ul>
                        </div>

                        <!-- Professional (Recommended) -->
                        <div @click="selectPlan('professional')" :class="form.plan === 'professional' ? 'border-blue-500 bg-blue-500/10 scale-105 shadow-[0_0_30px_rgba(59,130,246,0.2)]' : 'border-slate-700 bg-slate-900/50 hover:border-slate-500'" class="cursor-pointer border rounded-2xl p-6 transition-all relative z-10">
                            <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-blue-500 text-white text-[9px] font-black uppercase tracking-widest px-3 py-1 rounded-full">Recommended</div>
                            <h4 class="text-lg font-black text-white uppercase tracking-wider mb-1">Professional</h4>
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mb-4">Up to 50 Vehicles</p>
                            <div class="text-3xl font-black text-white mb-6">$299<span class="text-sm text-slate-500 font-normal">/mo</span></div>
                            <ul class="space-y-3 text-sm text-slate-300">
                                <li class="flex items-center gap-2"><span class="text-emerald-500">✔</span> Advanced Dispatch</li>
                                <li class="flex items-center gap-2"><span class="text-emerald-500">✔</span> Driver App</li>
                                <li class="flex items-center gap-2"><span class="text-emerald-500">✔</span> Predictive Maint.</li>
                            </ul>
                        </div>

                        <!-- Enterprise -->
                        <div @click="selectPlan('enterprise')" :class="form.plan === 'enterprise' ? 'border-rose-500 bg-rose-500/10' : 'border-slate-700 bg-slate-900/50 hover:border-slate-500'" class="cursor-pointer border rounded-2xl p-6 transition-all relative">
                            <h4 class="text-lg font-black text-white uppercase tracking-wider mb-1">Enterprise</h4>
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mb-4">Unlimited Scale</p>
                            <div class="text-3xl font-black text-white mb-6">Custom</div>
                            <ul class="space-y-3 text-sm text-slate-300">
                                <li class="flex items-center gap-2"><span class="text-emerald-500">✔</span> Dedicated Cluster</li>
                                <li class="flex items-center gap-2"><span class="text-emerald-500">✔</span> Accounting ERP API</li>
                                <li class="flex items-center gap-2"><span class="text-emerald-500">✔</span> Custom BI Dashboards</li>
                            </ul>
                        </div>
                    </div>

                    <p x-show="planError" class="text-rose-500 text-sm mt-4 text-center font-bold" x-text="planError"></p>

                    <div class="mt-8 flex justify-between">
                        <button type="button" @click="step--" class="text-slate-400 hover:text-white font-bold py-3 px-6 transition-all">&larr; Back</button>
                        <button type="button" @click="nextStep()" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-blue-900/20 transition-all">Review & Sign &rarr;</button>
                    </div>
                </div>

                <!-- STEP 4: Legal -->
                <div id="step-4" x-show="step === 4" style="display: none;" x-transition.opacity.duration.300ms>
                    <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-sm">4</span>
                        Master Service Agreement
                    </h3>

                    <div class="bg-slate-950 border border-slate-800 rounded-xl p-6 mb-6 h-64 overflow-y-auto prose prose-invert prose-sm max-w-none">
                        {!! $agreement ? $agreement->content : 'Agreement content not available at this time.' !!}
                    </div>

                    <div class="space-y-4 mb-8 bg-slate-900/50 p-6 rounded-xl border border-white/5">
                        <label class="flex items-start gap-3 cursor-pointer group">
                            <div class="flex items-center h-5">
                                <input x-model="form.agree_tos" type="checkbox" required class="w-5 h-5 bg-slate-800 border-slate-600 rounded text-emerald-500 focus:ring-emerald-500 focus:ring-opacity-25 transition duration-200">
                            </div>
                            <div class="text-sm">
                                <p class="text-white font-bold group-hover:text-emerald-400 transition-colors">I accept the Software Service Agreement Version {{ $agreement ? $agreement->version : 'N/A' }}</p>
                                <p class="text-slate-500 text-xs mt-1">By checking this box, you apply a legally binding electronic signature.</p>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 cursor-pointer group">
                            <div class="flex items-center h-5">
                                <input x-model="form.agree_data_policy" type="checkbox" required class="w-5 h-5 bg-slate-800 border-slate-600 rounded text-emerald-500 focus:ring-emerald-500 focus:ring-opacity-25 transition duration-200">
                            </div>
                            <div class="text-sm">
                                <p class="text-white font-bold group-hover:text-emerald-400 transition-colors">I accept the Data Processing & Privacy Policy</p>
                                <p class="text-slate-500 text-xs mt-1">We will protect your operational data as per international strictures.</p>
                            </div>
                        </label>
                    </div>

                    <!-- Global Errors -->
                    <div x-show="serverError" class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm font-bold" text-center x-html="serverError"></div>

                    <div class="mt-8 flex justify-between items-center">
                        <button type="button" @click="step--" class="text-slate-400 hover:text-white font-bold py-3 px-6 transition-all" :disabled="isSubmitting">&larr; Back</button>
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white font-black uppercase tracking-widest py-4 px-10 rounded-xl shadow-[0_0_20px_rgba(5,150,105,0.4)] transition-all transform hover:-translate-y-1 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-3" :disabled="!form.agree_tos || !form.agree_data_policy || isSubmitting">
                            <span x-show="!isSubmitting">Initialize Command Center</span>
                            <span x-show="isSubmitting">Provisioning Tenant...</span>
                            <svg x-show="isSubmitting" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

            </form>
        </div>

        <div class="mt-8 text-center">
            <p class="text-sm text-slate-500">
                Already have a fleet account? <a href="{{ route('login') }}" class="text-blue-400 hover:text-blue-300 font-bold transition-colors">Sign in to your dashboard</a>
            </p>
        </div>
    </div>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('registrationWizard', () => ({
            step: 1,
            planError: '',
            serverError: '',
            isSubmitting: false,
            form: {
                company_name: '',
                trade_license_number: '',
                trn_number: '',
                address: '',
                country: '',
                total_vehicles: '',
                contact_phone: '',
                registration_source: 'direct',
                owner_name: '',
                owner_email: '',
                owner_password: '',
                owner_password_confirmation: '',
                plan: 'professional',
                agree_tos: false,
                agree_data_policy: false,
                agreement_version: "{{ $agreement ? $agreement->version : '' }}",
            },

            nextStep() {
                // Validate only visible inputs in current step
                const currentStepDiv = document.getElementById(`step-${this.step}`);
                if (currentStepDiv) {
                    const inputs = currentStepDiv.querySelectorAll('input[required], select[required]');
                    let isValid = true;
                    for (let input of inputs) {
                        if (!input.checkValidity()) {
                            input.reportValidity();
                            isValid = false;
                            break;
                        }
                    }
                    if (!isValid) return;
                }

                if (this.step === 3) {
                    this.planError = '';
                    if (parseInt(this.form.total_vehicles) > 50 && this.form.plan !== 'enterprise') {
                        this.planError = 'Fleets with over 50 vehicles require the Custom Enterprise plan.';
                        return;
                    }
                }

                if (this.step < 4) {
                    this.step++;
                }
            },

            selectPlan(plan) {
                this.form.plan = plan;
                this.planError = '';
            },

            async submitForm() {
                if (!this.form.agree_tos || !this.form.agree_data_policy) return;

                this.isSubmitting = true;
                this.serverError = '';

                try {
                    const response = await axios.post('{{ route("register.process") }}', this.form, {
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    if (response.data.success) {
                        window.location.href = response.data.redirect_url;
                    }
                } catch (error) {
                    this.isSubmitting = false;

                    if (error.response && error.response.status === 422) {
                        // Validation error
                        const errors = error.response.data.errors;
                        this.serverError = '<ul>';
                        for (let key in errors) {
                            this.serverError += `<li>${errors[key][0]}</li>`;
                        }
                        this.serverError += '</ul>';

                        // Drop back to step where error occurred if possible
                        if (errors.company_name || errors.trade_license_number || errors.total_vehicles) this.step = 1;
                        else if (errors.owner_email || errors.owner_password) this.step = 2;
                        else if (errors.plan) this.step = 3;
                    } else {
                        // Other error
                        this.serverError = error.response?.data?.message || 'An unexpected error occurred during provisioning.';
                    }
                }
            }
        }));
    });
</script>
@endsection