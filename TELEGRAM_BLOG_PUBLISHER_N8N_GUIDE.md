# 📱 Telegram Blog Publisher + n8n Integration Guide

## 🎯 What This Guide Covers
This guide will help you set up the Telegram Blog Publisher plugin with n8n to automatically create WordPress blog posts from Telegram messages.

---

## 📋 Prerequisites

### What You Need:
1. **WordPress Website** with the Telegram Blog Publisher plugin installed
2. **n8n Instance** (self-hosted or cloud)
3. **Telegram Bot** (we'll create this)
4. **AI API Key** (Gemini, OpenAI, Claude, etc.)

---

## 🤖 Step 1: Create a Telegram Bot

### 1.1 Open Telegram
- Open Telegram app on your phone or computer
- Search for `@BotFather`

### 1.2 Create New Bot
1. Send `/newbot` to BotFather
2. Choose a name for your bot (e.g., "My Blog Bot")
3. Choose a username (e.g., "myblog_bot")
4. **Save the Bot Token** - you'll need this later!

### 1.3 Get Your Chat ID
1. Send a message to your new bot
2. Visit: `https://api.telegram.org/bot<YOUR_BOT_TOKEN>/getUpdates`
3. Look for `"chat":{"id":123456789}` - this is your Chat ID

---

## 🔧 Step 2: Configure WordPress Plugin

### 2.1 Install Plugin
1. Go to your WordPress admin panel
2. Navigate to **Plugins** → **Add New**
3. Upload the `telegram-blog-publisher.zip` file
4. **Activate** the plugin

### 2.2 Configure API Keys
1. Go to **Telegram Blog** → **Settings**
2. Enter your **Webhook Secret** (create a random string like `mysecret123`)
3. Choose an AI service and enter your API key:
   - **Gemini** (Free): Get from [Google AI Studio](https://aistudio.google.com/)
   - **OpenAI** (Paid): Get from [OpenAI Platform](https://platform.openai.com/)
   - **Claude** (Paid): Get from [Anthropic Console](https://console.anthropic.com/)

### 2.3 Test Your Setup
1. Click **Test** next to your API key
2. You should see "✓ Working" if successful

---

## 🔗 Step 3: Set Up n8n Workflow

### 3.1 Create New Workflow
1. Open your n8n instance
2. Click **"New Workflow"**
3. Name it "Telegram to WordPress Blog"

### 3.2 Add Telegram Trigger
1. Click **"Add Trigger"**
2. Search for **"Telegram Trigger"**
3. Configure:
   - **Bot Token**: Your bot token from Step 1.2
   - **Updates**: Select "Message"
   - **Additional Fields**: Add `chat_id` and set to your Chat ID

### 3.3 Add Webhook Node
1. Add **"Webhook"** node after Telegram Trigger
2. Configure:
   - **HTTP Method**: POST
   - **Path**: `/telegram-webhook`
   - **Response Mode**: "Using 'Respond to Webhook' Node"
3. **Copy the Webhook URL** - you'll need this!

### 3.4 Add HTTP Request Node
1. Add **"HTTP Request"** node after Webhook
2. Configure:
   - **Method**: POST
   - **URL**: `https://yourwebsite.com/wp-json/telegram-blog-publisher/v1/webhook`
   - **Headers**:
     - `Content-Type`: `application/json`
     - `X-Webhook-Secret`: `your_webhook_secret_from_step_2.2`
   - **Body**:
     ```json
     {
       "topic": "{{ $json.message.text }}",
       "title": "Blog Post from Telegram",
       "secret": "your_webhook_secret_from_step_2.2"
     }
     ```

### 3.5 Add Response Node
1. Add **"Respond to Webhook"** node
2. Configure:
   - **Response Body**:
     ```json
     {
       "status": "success",
       "message": "Blog post created successfully!"
     }
     ```

---

## 🔄 Step 4: Connect Everything

### 4.1 Connect the Nodes
```
Telegram Trigger → Webhook → HTTP Request → Respond to Webhook
```

### 4.2 Visual Workflow
```
📱 Telegram Message
    ↓
🤖 n8n Telegram Trigger
    ↓
🔗 n8n Webhook Node
    ↓
🌐 HTTP Request to WordPress
    ↓
📝 WordPress Creates Blog Post
    ↓
✅ Response Back to n8n
    ↓
📱 Confirmation to User
```

### 4.3 Data Flow
1. **User sends message** to Telegram bot
2. **n8n receives** the message via Telegram Trigger
3. **n8n processes** the message and sends to WordPress
4. **WordPress AI** generates blog post content
5. **WordPress creates** the blog post
6. **n8n sends** confirmation back to user

### 4.2 Test the Workflow
1. **Activate** the workflow in n8n
2. Send a message to your Telegram bot
3. Check if a blog post was created in WordPress

---

## 📝 Step 5: Advanced Configuration

### 5.1 Customize Blog Post Content
In the HTTP Request node, you can modify the body to include more details:

```json
{
  "topic": "{{ $json.message.text }}",
  "title": "{{ $json.message.text }}",
  "word_count": 500,
  "tone": "professional",
  "secret": "your_webhook_secret"
}
```

### 5.2 Add Error Handling
1. Add **"IF"** node after HTTP Request
2. Check if response status is 200
3. If error, send different response

### 5.3 Add Notifications
1. Add **"Telegram"** node for success/error notifications
2. Send confirmation back to user

---

## 🛠️ Troubleshooting

### Common Issues:

#### ❌ "API key not working"
- **Solution**: Check your API key in WordPress settings
- **Test**: Use the "Test" button next to your API key

#### ❌ "Webhook not responding"
- **Solution**: Check your webhook URL is correct
- **Test**: Visit the webhook URL directly in browser

#### ❌ "Blog post not created"
- **Solution**: Check WordPress logs
- **Test**: Try manual webhook test in WordPress

#### ❌ "n8n workflow not triggering"
- **Solution**: Check Telegram bot token
- **Test**: Send `/start` to your bot

---

## 🎉 Step 6: Go Live!

### 6.1 Final Checklist
- [ ] Telegram bot created and tested
- [ ] WordPress plugin configured with API key
- [ ] n8n workflow created and activated
- [ ] Webhook URL configured correctly
- [ ] Test message sent successfully

### 6.2 Start Using!
1. Send any message to your Telegram bot
2. Watch as it automatically creates a WordPress blog post
3. Check your WordPress admin to see the new post

---

## 🔧 Advanced Features

### Custom Prompts
You can customize how the AI generates content by modifying the prompt in the plugin settings.

### Multiple AI Services
The plugin supports multiple AI services - you can switch between them anytime.

### Content Quality Settings
- **Basic**: 300-500 words
- **Standard**: 500-800 words  
- **Premium**: 800-1200 words
- **Enterprise**: 1200+ words

### SEO Optimization
The plugin automatically adds:
- Meta descriptions
- SEO-friendly titles
- Structured data
- Image placeholders

---

## 📞 Need Help?

### Check These First:
1. **WordPress Error Logs**: Go to WordPress admin → Tools → Site Health
2. **n8n Execution Logs**: Check the execution details in n8n
3. **Telegram Bot Status**: Make sure your bot is active

### Common Solutions:
- **Restart n8n workflow** if it stops working
- **Regenerate API keys** if they expire
- **Check webhook URL** if posts aren't created
- **Verify bot permissions** if Telegram doesn't work

---

## 🎯 Quick Start Summary

1. **Create Telegram Bot** → Get token and chat ID
2. **Install WordPress Plugin** → Configure API key
3. **Create n8n Workflow** → Connect Telegram to WordPress
4. **Test Everything** → Send message to bot
5. **Go Live!** → Start creating blog posts automatically

**That's it! You now have an automated blog creation system! 🚀**

---

## ✅ Setup Checklist

### Telegram Bot Setup
- [ ] Created bot with @BotFather
- [ ] Saved bot token
- [ ] Got chat ID from getUpdates
- [ ] Bot responds to messages

### WordPress Plugin Setup
- [ ] Plugin installed and activated
- [ ] Webhook secret configured
- [ ] AI API key added and tested
- [ ] Webhook URL working

### n8n Workflow Setup
- [ ] Telegram Trigger configured
- [ ] Webhook node added
- [ ] HTTP Request to WordPress configured
- [ ] Response node added
- [ ] Workflow activated

### Testing
- [ ] Sent test message to bot
- [ ] Blog post created in WordPress
- [ ] No errors in logs
- [ ] Confirmation received

**All checked? You're ready to go! 🎉**

---

*Need more help? Check the plugin documentation or contact support.*
