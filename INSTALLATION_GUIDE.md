# 📱 Telegram Blog Publisher - Installation Guide

## 🎯 Complete Setup Instructions

This guide will walk you through installing and configuring the Telegram Blog Publisher plugin on your WordPress website.

---

## 📋 Prerequisites

### What You Need:
1. **WordPress Website** (version 5.0 or higher)
2. **Admin Access** to your WordPress site
3. **AI API Key** (Gemini, OpenAI, Claude, etc.)
4. **n8n Instance** (optional, for automation)

---

## 🚀 Step 1: Download the Plugin

### 1.1 Get the Plugin File
1. Download `telegram-blog-publisher.zip` from the repository
2. Save it to your computer

### 1.2 Alternative: Clone from GitHub
```bash
git clone https://github.com/barcodemine-creator/barcodemine-wordpress.git
cd barcodemine-wordpress
```

---

## 📦 Step 2: Install the Plugin

### 2.1 Via WordPress Admin (Recommended)
1. **Login** to your WordPress admin panel
2. Go to **Plugins** → **Add New**
3. Click **"Upload Plugin"**
4. Choose the `telegram-blog-publisher.zip` file
5. Click **"Install Now"**
6. Click **"Activate Plugin"**

### 2.2 Via FTP (Alternative)
1. **Extract** the zip file on your computer
2. **Upload** the `telegram-blog-publisher` folder to `/wp-content/plugins/`
3. **Login** to WordPress admin
4. Go to **Plugins** → **Installed Plugins**
5. Find **"Telegram Blog Publisher Premium"**
6. Click **"Activate"**

---

## ⚙️ Step 3: Configure the Plugin

### 3.1 Access Plugin Settings
1. In WordPress admin, look for **"Telegram Blog"** in the sidebar
2. Click on **"Settings"**

### 3.2 Set Up Webhook Secret
1. **Generate a secret key** (use a random string like `mysecret123456`)
2. Enter it in the **"Webhook Secret"** field
3. **Save** this secret - you'll need it for n8n

### 3.3 Configure AI Service
Choose one of these AI services:

#### Option A: Google Gemini (Free)
1. Go to [Google AI Studio](https://aistudio.google.com/)
2. Click **"Get API Key"**
3. Create a new API key
4. Copy the key (starts with `AIza...`)
5. Paste it in the **"Google Gemini"** field
6. Click **"Test"** to verify

#### Option B: OpenAI (Paid)
1. Go to [OpenAI Platform](https://platform.openai.com/)
2. Navigate to **"API Keys"**
3. Create a new secret key
4. Copy the key (starts with `sk-...`)
5. Paste it in the **"OpenAI"** field
6. Click **"Test"** to verify

#### Option C: Claude (Paid)
1. Go to [Anthropic Console](https://console.anthropic.com/)
2. Navigate to **"API Keys"**
3. Create a new key
4. Copy the key (starts with `sk-ant-...`)
5. Paste it in the **"Claude"** field
6. Click **"Test"** to verify

### 3.4 Save Settings
1. Click **"Save All Settings"**
2. Verify all API keys show **"✓ Working"**

---

## 🔗 Step 4: Get Your Webhook URL

### 4.1 Find Your Webhook URL
1. Go to **"Telegram Blog"** → **"Dashboard"**
2. Look for the **"Webhook Information"** section
3. Copy the **Webhook URL** (it will be unique to your website)
4. Copy the **Webhook Secret**

### 4.2 Test the Webhook
1. Go to **"Telegram Blog"** → **"Webhook Testing"**
2. Click **"Test Local Webhook"**
3. You should see **"✓ Webhook is working!"**

---

## 🤖 Step 5: Set Up Telegram Bot (Optional)

### 5.1 Create Telegram Bot
1. Open Telegram app
2. Search for `@BotFather`
3. Send `/newbot`
4. Choose a name: `My Blog Bot`
5. Choose a username: `myblog_bot`
6. **Save the Bot Token**

### 5.2 Get Your Chat ID
1. Send a message to your new bot
2. Visit: `https://api.telegram.org/bot<YOUR_BOT_TOKEN>/getUpdates`
3. Find your Chat ID in the response

---

## 🔄 Step 6: Configure n8n (Optional)

### 6.1 Create n8n Workflow
1. Open your n8n instance
2. Create a new workflow
3. Add these nodes:

```
Telegram Trigger → Webhook → HTTP Request → Respond to Webhook
```

### 6.2 Configure Nodes
1. **Telegram Trigger**:
   - Bot Token: Your bot token
   - Updates: Message

2. **HTTP Request**:
   - Method: POST
   - URL: Your webhook URL from Step 4.1
   - Headers:
     - `Content-Type`: `application/json`
     - `X-Webhook-Secret`: Your webhook secret
   - Body:
     ```json
     {
       "topic": "{{ $json.message.text }}",
       "title": "Blog Post from Telegram",
       "secret": "your_webhook_secret"
     }
     ```

3. **Respond to Webhook**:
   - Response Body:
     ```json
     {
       "status": "success",
       "message": "Blog post created!"
     }
     ```

---

## ✅ Step 7: Test Everything

### 7.1 Test the Plugin
1. Go to **"Telegram Blog"** → **"Generate Content"**
2. Enter a topic: `"How to use WordPress"`
3. Click **"Generate Blog Post"**
4. Check if a new post appears in **"Posts"** → **"All Posts"**

### 7.2 Test with Telegram (if configured)
1. Send a message to your Telegram bot
2. Check if a blog post was created
3. Verify the post appears in WordPress

---

## 🛠️ Troubleshooting

### Common Issues:

#### ❌ "Plugin not activating"
- **Solution**: Check WordPress version (needs 5.0+)
- **Check**: PHP version (needs 7.4+)

#### ❌ "API key not working"
- **Solution**: Verify API key is correct
- **Test**: Use the "Test" button next to each API key

#### ❌ "Webhook not responding"
- **Solution**: Check if your website supports REST API
- **Test**: Visit `yoursite.com/wp-json/` in browser

#### ❌ "Blog post not created"
- **Solution**: Check WordPress error logs
- **Test**: Try manual content generation first

---

## 📊 Step 8: Monitor Usage

### 8.1 Check Dashboard
- Go to **"Telegram Blog"** → **"Dashboard"**
- View recent posts and statistics
- Monitor AI service status

### 8.2 View Logs
- Go to **"Telegram Blog"** → **"System Status"**
- Check for any errors or issues
- Clear logs if needed

---

## 🎯 Quick Start Checklist

### Installation
- [ ] WordPress site ready (5.0+)
- [ ] Plugin uploaded and activated
- [ ] No activation errors

### Configuration
- [ ] Webhook secret set
- [ ] AI API key configured and tested
- [ ] Webhook URL working

### Testing
- [ ] Manual content generation works
- [ ] Blog posts appear in WordPress
- [ ] No errors in logs

### Optional Setup
- [ ] Telegram bot created
- [ ] n8n workflow configured
- [ ] End-to-end automation working

---

## 🔧 Advanced Configuration

### Content Quality Settings
- **Basic**: 300-500 words
- **Standard**: 500-800 words
- **Premium**: 800-1200 words
- **Enterprise**: 1200+ words

### SEO Features
- Auto-generated meta descriptions
- SEO-friendly titles
- Structured data
- Image placeholders

### Multiple AI Services
- Switch between AI providers
- Fallback options
- Cost optimization

---

## 📞 Need Help?

### Check These First:
1. **WordPress Error Logs**: Go to Tools → Site Health
2. **Plugin Status**: Check if all components are active
3. **API Key Status**: Verify all services are working

### Common Solutions:
- **Restart WordPress** if plugin stops working
- **Regenerate API keys** if they expire
- **Check webhook URL** if automation fails
- **Verify permissions** if posts aren't created

---

## 🎉 You're All Set!

Once everything is configured:

1. **Send messages** to your Telegram bot
2. **Watch** as blog posts are created automatically
3. **Manage** your content from WordPress admin
4. **Monitor** usage and performance

**Your automated blog creation system is now live! 🚀**

---

*For more help, check the plugin documentation or contact support.*
