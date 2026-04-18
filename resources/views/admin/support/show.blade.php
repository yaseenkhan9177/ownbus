@extends('layouts.super-admin')

@section('title', 'Ticket #' . str_pad($ticket->id, 5, '0', STR_PAD_LEFT) . ' | SaaS Admin')

@section('header_title')
<div class="flex items-center space-x-4">
    <a href="{{ route('admin.support.index') }}" class="text-slate-500 hover:text-cyan-400 transition-colors">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
    </a>
    <h1 class="text-xl font-bold text-slate-100 tracking-wide flex items-center">
        <span class="text-slate-500 mr-3">#{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}</span>
        {{ $ticket->subject }}
    </h1>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Chat Thread (Left Column - 2/3 width) -->
    <div class="lg:col-span-2 space-y-6">

        <!-- Messages Container -->
        <div class="bg-[#0f1524] rounded-xl border border-slate-800 shadow-lg overflow-hidden flex flex-col h-[65vh]">

            <!-- Header -->
            <div class="px-6 py-4 border-b border-slate-800 bg-slate-900/50 flex justify-between items-center shrink-0">
                <h2 class="text-sm font-semibold text-slate-300 uppercase tracking-widest flex items-center">
                    <svg class="h-4 w-4 mr-2 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                    </svg>
                    Conversation Log
                </h2>
                <span class="text-xs font-mono text-slate-500">
                    Opened {{ $ticket->created_at->format('M d, Y h:i A') }}
                </span>
            </div>

            <!-- Messages Stream -->
            <div class="flex-1 overflow-y-auto p-6 space-y-6 bg-slate-900/20" id="chatContainer">

                @forelse($replies as $reply)
                @if($reply->is_admin_reply)
                <!-- Admin Reply (Right aligned) -->
                <div class="flex flex-col items-end w-full">
                    <div class="flex items-end space-x-2 max-w-[80%]">
                        <div class="bg-cyan-500/10 border border-cyan-500/30 text-slate-300 p-4 rounded-2xl rounded-br-none shadow-sm relative">
                            <div class="text-sm break-words leading-relaxed whitespace-pre-wrap">{{ $reply->body }}</div>
                            <div class="text-[10px] text-cyan-500/70 text-right mt-2 font-mono">
                                {{ $reply->user->name }} • {{ $reply->created_at->format('h:i A') }}
                            </div>
                        </div>
                        <div class="w-8 h-8 rounded-full bg-cyan-600 flex-shrink-0 flex items-center justify-center text-xs font-bold text-white shadow-md border-2 border-[#0f1524]">
                            {{ substr($reply->user->name, 0, 1) }}
                        </div>
                    </div>
                </div>
                @else
                <!-- Tenant Reply (Left aligned) -->
                <div class="flex flex-col items-start w-full">
                    <div class="flex items-end space-x-3 max-w-[80%]">
                        <div class="w-8 h-8 rounded-full bg-slate-700 flex-shrink-0 flex items-center justify-center text-xs font-bold text-slate-300 shadow-md border-2 border-[#0f1524]">
                            {{ substr($reply->user->name, 0, 1) }}
                        </div>
                        <div class="bg-slate-800 text-slate-300 p-4 rounded-2xl rounded-bl-none shadow-sm border border-slate-700">
                            <div class="text-sm break-words leading-relaxed whitespace-pre-wrap">{{ $reply->body }}</div>
                            <div class="text-[10px] text-slate-500 mt-2 font-mono">
                                {{ $reply->user->name }} • {{ $reply->created_at->format('M d, h:i A') }}
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @empty
                <div class="text-center text-slate-500 italic py-10">
                    No messages in this thread yet.
                </div>
                @endforelse

            </div>

            <!-- Reply Box -->
            @if($ticket->status !== 'closed')
            <div class="px-6 py-4 border-t border-slate-800 bg-slate-900/50 shrink-0">
                <form action="{{ route('admin.support.reply', $ticket->id) }}" method="POST">
                    @csrf
                    <div class="relative">
                        <textarea name="body" rows="3" required
                            class="w-full bg-[#0f1524] border border-slate-700 rounded-lg pl-4 pr-32 py-3 text-sm text-slate-300 placeholder-slate-600 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-colors resize-none shadow-inner"
                            placeholder="Write a response to the tenant..."></textarea>
                        <div class="absolute bottom-3 right-3">
                            <button type="submit" class="bg-cyan-600 hover:bg-cyan-500 text-white text-sm font-semibold px-6 py-2 rounded-md shadow-[0_0_15px_rgba(6,182,212,0.4)] transition-all flex items-center">
                                <span>Send</span>
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            @else
            <div class="px-6 py-4 border-t border-rose-900/30 bg-rose-500/5 shrink-0 text-center">
                <span class="text-rose-400 font-semibold text-sm">This ticket has been closed. Replies are disabled.</span>
            </div>
            @endif

        </div>

    </div>

    <!-- Metadata Sidebar (Right Column - 1/3 width) -->
    <div class="space-y-6 lg:col-span-1">

        <!-- Status Control -->
        <div class="bg-[#0f1524] rounded-xl border border-slate-800 shadow-lg p-6">
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-4">Ticket Execution</h3>

            <form action="{{ route('admin.support.update-status', $ticket->id) }}" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-xs text-slate-400 mb-2">Current Status</label>
                    <div class="relative">
                        <select name="status" class="w-full bg-slate-900 border border-slate-700 text-slate-300 text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 block p-2.5 appearance-none font-semibold">
                            <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 hover:border-slate-500 text-sm font-semibold py-2.5 rounded-lg transition-all">
                    Update Workflow
                </button>
            </form>
        </div>

        <!-- Tenant Identity -->
        <div class="bg-[#0f1524] rounded-xl border border-slate-800 shadow-lg p-6">
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-4 border-b border-slate-800 pb-2">Tenant Profile</h3>

            <div class="space-y-4">
                <div>
                    <p class="text-[10px] text-slate-500 uppercase tracking-widest">Organization</p>
                    <p class="font-bold text-slate-200">{{ $ticket->company->name }}</p>
                </div>

                <div>
                    <p class="text-[10px] text-slate-500 uppercase tracking-widest">Reporter</p>
                    <p class="font-bold text-slate-300 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        {{ $ticket->user->name }}
                    </p>
                    <p class="text-xs text-slate-400 mt-1 pl-6">{{ $ticket->user->email }}</p>
                </div>

                @if($ticket->company->subscription && $ticket->company->subscription->plan)
                <div>
                    <p class="text-[10px] text-slate-500 uppercase tracking-widest mb-1">Active Tier</p>
                    <span class="inline-block px-2 py-1 bg-cyan-900/30 text-cyan-400 border border-cyan-800 rounded text-[10px] font-bold uppercase tracking-widest">
                        {{ $ticket->company->subscription->plan->name }}
                    </span>
                </div>
                @endif
            </div>
        </div>

        <!-- Ticket Physics -->
        <div class="bg-[#0f1524] rounded-xl border border-slate-800 shadow-lg p-6">
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-4 border-b border-slate-800 pb-2">Diagnostics</h3>

            <div class="space-y-4">
                <div>
                    <p class="text-[10px] text-slate-500 uppercase tracking-widest">Severity Priority</p>
                    @if($ticket->priority === 'critical')
                    <p class="font-black text-rose-500 uppercase tracking-widest mt-1">CRITICAL</p>
                    @elseif($ticket->priority === 'high')
                    <p class="font-bold text-orange-400 uppercase tracking-widest mt-1">HIGH</p>
                    @elseif($ticket->priority === 'medium')
                    <p class="font-bold text-amber-500 uppercase tracking-widest mt-1">MEDIUM</p>
                    @else
                    <p class="font-bold text-slate-400 uppercase tracking-widest mt-1">LOW</p>
                    @endif
                </div>

                <div>
                    <p class="text-[10px] text-slate-500 uppercase tracking-widest">Internal ID</p>
                    <p class="font-mono text-slate-400 text-xs mt-1">{{ $ticket->uuid }}</p>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    // Auto-scroll chat to bottom on load
    document.addEventListener("DOMContentLoaded", function() {
        var chatBox = document.getElementById("chatContainer");
        if (chatBox) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    });
</script>
@endpush