/**
 * Messaging Client Library
 * 
 * Provides real-time messaging via WebSocket with AJAX polling fallback.
 * 
 * Usage:
 *   const chat = new MessagingClient({
 *     user: { tentk: 'username', vaitro: 1 },
 *     onMessage: (msg) => { ... },
 *     onConnected: () => { ... },
 *     onError: (err) => { ... }
 *   });
 *   chat.connect();
 *   chat.sendMessage('receiver', 'Hello!');
 */

class MessagingClient {
    constructor(options = {}) {
        this.user = options.user || {};
        this.onMessage = options.onMessage || function() {};
        this.onConnected = options.onConnected || function() {};
        this.onDisconnected = options.onDisconnected || function() {};
        this.onError = options.onError || function() {};
        this.onMessagesLoaded = options.onMessagesLoaded || function() {};
        this.onSent = options.onSent || function() {};
        
        this.socket = null;
        this.isConnected = false;
        this.useWebSocket = true;
        this.pollingInterval = null;
        this.lastTimestamp = null;
        this.currentPartner = null;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 5;
        this.reconnectDelay = 3000;
        
        // Default configuration
        this.config = {
            websocket: {
                enabled: true,
                url: 'ws://localhost:8080'
            },
            polling: {
                interval: 3000
            },
            apiUrl: 'Ajax/chat_api.php'
        };
    }
    
    /**
     * Initialize by loading config from server
     */
    async init() {
        try {
            const response = await fetch(this.config.apiUrl + '?action=get_config');
            const data = await response.json();
            
            if (data.success) {
                this.config.websocket.enabled = data.websocket.enabled;
                this.config.websocket.url = data.websocket.url;
                this.config.polling.interval = data.polling.interval;
            }
        } catch (error) {
            console.warn('Failed to load config, using defaults:', error);
        }
        
        return this;
    }
    
    /**
     * Connect to messaging service
     */
    connect() {
        if (this.config.websocket.enabled) {
            this.connectWebSocket();
        } else {
            this.startPolling();
        }
    }
    
    /**
     * Connect via WebSocket
     */
    connectWebSocket() {
        try {
            this.socket = new WebSocket(this.config.websocket.url);
            
            this.socket.onopen = () => {
                console.log('✅ WebSocket connected');
                this.isConnected = true;
                this.useWebSocket = true;
                this.reconnectAttempts = 0;
                
                // Register user
                this.socket.send(JSON.stringify({
                    command: 'register',
                    username: this.user.tentk,
                    role: this.user.vaitro
                }));
                
                this.onConnected();
            };
            
            this.socket.onmessage = (event) => {
                const data = JSON.parse(event.data);
                this.handleServerMessage(data);
            };
            
            this.socket.onclose = () => {
                console.warn('⚠️ WebSocket closed');
                this.isConnected = false;
                this.onDisconnected();
                
                // Try to reconnect or fallback to polling
                if (this.reconnectAttempts < this.maxReconnectAttempts) {
                    this.reconnectAttempts++;
                    console.log(`Reconnecting (attempt ${this.reconnectAttempts})...`);
                    setTimeout(() => this.connectWebSocket(), this.reconnectDelay);
                } else {
                    console.log('Max reconnect attempts reached, falling back to polling');
                    this.fallbackToPolling();
                }
            };
            
            this.socket.onerror = (error) => {
                console.error('WebSocket error:', error);
                this.onError(error);
            };
        } catch (error) {
            console.error('Failed to create WebSocket:', error);
            this.fallbackToPolling();
        }
    }
    
    /**
     * Fallback to AJAX polling
     */
    fallbackToPolling() {
        this.useWebSocket = false;
        this.startPolling();
        this.onConnected(); // Still notify connected via polling
    }
    
    /**
     * Start AJAX polling
     */
    startPolling() {
        console.log('📡 Starting AJAX polling');
        this.useWebSocket = false;
        this.isConnected = true;
        
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
        }
        
        this.pollingInterval = setInterval(() => {
            if (this.currentPartner) {
                this.pollNewMessages();
            }
        }, this.config.polling.interval);
    }
    
    /**
     * Stop polling
     */
    stopPolling() {
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
            this.pollingInterval = null;
        }
    }
    
    /**
     * Poll for new messages via AJAX
     */
    async pollNewMessages() {
        if (!this.currentPartner) return;
        
        try {
            const params = new URLSearchParams({
                action: 'check_new_messages',
                partner: this.currentPartner,
                last_timestamp: this.lastTimestamp || ''
            });
            
            const response = await fetch(this.config.apiUrl + '?' + params.toString());
            const data = await response.json();
            
            if (data.success && data.messages && data.messages.length > 0) {
                data.messages.forEach(msg => {
                    // Only show messages from partner (not our own)
                    if (msg.sender !== this.user.tentk) {
                        this.onMessage({
                            command: 'receive',
                            sender: msg.sender,
                            message: msg.message,
                            time: msg.time,
                            messageId: msg.messageId
                        });
                    }
                });
                this.lastTimestamp = data.timestamp;
            }
        } catch (error) {
            console.error('Polling error:', error);
        }
    }
    
    /**
     * Handle messages from server (WebSocket)
     */
    handleServerMessage(data) {
        switch (data.command) {
            case 'messages':
                this.onMessagesLoaded(data.messages, data.receiver_tentk);
                if (data.messages && data.messages.length > 0) {
                    const lastMsg = data.messages[data.messages.length - 1];
                    this.lastTimestamp = lastMsg.time;
                }
                break;
                
            case 'receive':
            case 'receive_file':
                this.onMessage(data);
                if (data.time) {
                    this.lastTimestamp = data.time;
                }
                break;
                
            case 'sent':
            case 'file_sent':
                this.onSent(data);
                break;
                
            default:
                console.log('Unknown command:', data);
        }
    }
    
    /**
     * Send a text message
     */
    async sendMessage(receiver, message) {
        if (this.useWebSocket && this.socket && this.socket.readyState === WebSocket.OPEN) {
            // Send via WebSocket
            this.socket.send(JSON.stringify({
                command: 'send',
                sender: this.user.tentk,
                receiver: receiver,
                message: message
            }));
        } else {
            // Send via AJAX
            try {
                const formData = new FormData();
                formData.append('action', 'send_message');
                formData.append('receiver', receiver);
                formData.append('message', message);
                
                const response = await fetch(this.config.apiUrl, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.onSent({
                        command: 'sent',
                        receiver: receiver,
                        message: message,
                        time: data.time,
                        messageId: data.messageId
                    });
                } else {
                    this.onError(data.error);
                }
            } catch (error) {
                console.error('Send message error:', error);
                this.onError(error);
            }
        }
    }
    
    /**
     * Send a file message
     */
    sendFile(receiver, filename, url) {
        if (this.useWebSocket && this.socket && this.socket.readyState === WebSocket.OPEN) {
            this.socket.send(JSON.stringify({
                command: 'send_file',
                sender: this.user.tentk,
                receiver: receiver,
                filename: filename,
                url: url
            }));
        }
    }
    
    /**
     * Load message history with a partner
     */
    async loadMessages(partner) {
        this.currentPartner = partner;
        this.lastTimestamp = null;
        
        if (this.useWebSocket && this.socket && this.socket.readyState === WebSocket.OPEN) {
            // Load via WebSocket
            this.socket.send(JSON.stringify({
                command: 'load_messages',
                tentk: this.user.tentk,
                receiver_tentk: partner
            }));
        } else {
            // Load via AJAX
            try {
                const params = new URLSearchParams({
                    action: 'load_messages',
                    partner: partner
                });
                
                const response = await fetch(this.config.apiUrl + '?' + params.toString());
                const data = await response.json();
                
                if (data.success) {
                    this.onMessagesLoaded(data.messages, partner);
                    if (data.messages && data.messages.length > 0) {
                        const lastMsg = data.messages[data.messages.length - 1];
                        this.lastTimestamp = lastMsg.time;
                    } else {
                        this.lastTimestamp = data.timestamp;
                    }
                } else {
                    this.onError(data.error);
                }
            } catch (error) {
                console.error('Load messages error:', error);
                this.onError(error);
            }
        }
    }
    
    /**
     * Set current partner for polling
     */
    setCurrentPartner(partner) {
        this.currentPartner = partner;
    }
    
    /**
     * Disconnect from messaging service
     */
    disconnect() {
        this.stopPolling();
        
        if (this.socket) {
            this.socket.close();
            this.socket = null;
        }
        
        this.isConnected = false;
    }
    
    /**
     * Check if connected
     */
    isReady() {
        if (this.useWebSocket) {
            return this.socket && this.socket.readyState === WebSocket.OPEN;
        }
        return this.isConnected;
    }
}

// Export for use in different module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = MessagingClient;
}
