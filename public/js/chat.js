var app = new Vue({
    el: '#app',
    components: {
        message: {
            props: ['sender', 'message', 'createdat'],
            template: '',
            // template: '<div><b>{{ sender }}</b> <sub class="createdat">{{ createdat | showChatTime }}</sub><p>{{ message }}</p></div>',
            filters: {
                showChatTime: function (createdat) {
                    var date = new Date(createdat);
                    date = ("0" + date.getDate()).slice(-2) + '/' + ("0" + date.getMonth()).slice(-2) + '/' + date.getFullYear() + ' ' +
                        ("0" + date.getHours()).slice(-2) + ':' + ("0" + date.getMinutes()).slice(-2);
                    return createdat;
                }
            }
        },
    },
    data: {
        messages: [],
        message: '',
        isTyping: '',
        onlineUsers: [],
        userName : '',
        template : '',
        st : 'sent',
        rt : 'receive'
    },
    methods: {
        sendMessage: function (event) {
            if (this.message.trim() == '' || this.message.trim == null) {
                return;
            }
            var th = this;
            axios.post(postChatURL, {
                'message': th.message
            })
                .then(function (response) {
                    console.log(response);
                    th.message = '';
                    th.messages.push(response.data);
                    // th.fetchChat();
                    th.adjustChatContainer();
                })
                .catch(function (error) {
                    console.log(error);
                })
        },
        fetchChat: function () {
            var th = this;
            axios.get(fetchChatURL)
                .then(function (response) {
                    console.log('chat fetched => ', response)
                    th.messages = response.data;
                    th.adjustChatContainer();
                })
                .catch(function (error) {
                    console.log(error);
                })
        },
        updateChat: function (res) {
            console.log('chat broadcasted => ',res);
            this.messages.push(res.message);
        },
        adjustChatContainer: function () {
            var chatContainer = document.getElementById('messages');
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        },
        userIsTyping: function (chatRoomId) {
            window.Echo.private('chat-room-'+chatRoomId)
                .whisper('typing', {
                    user: this.userName
                });
        }
    },
    mounted: function () {
        this.$nextTick(function () {
            this.userName = window.Laravel.user;
            if(fetchChatURL) {
                this.fetchChat();
            }
        })
    },
    updated: function () {
        this.adjustChatContainer();
    }
})