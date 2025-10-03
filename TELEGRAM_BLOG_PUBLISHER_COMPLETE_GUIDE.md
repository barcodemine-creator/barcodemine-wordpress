# 📱 Telegram Blog Publisher - Complete Setup Guide

## 🎯 Overview
This guide will walk you through setting up the complete Telegram Blog Publisher workflow, from installing the WordPress plugin to creating n8n workflows and connecting everything together.

## 📋 Table of Contents
1. [Prerequisites](#prerequisites)
2. [WordPress Plugin Installation](#wordpress-plugin-installation)
3. [Plugin Configuration](#plugin-configuration)
4. [n8n Workflow Setup](#n8n-workflow-setup)
5. [Telegram Bot Setup](#telegram-bot-setup)
6. [Testing the Complete Workflow](#testing-the-complete-workflow)
7. [Troubleshooting](#troubleshooting)
8. [Advanced Configuration](#advanced-configuration)

---

## 1. Prerequisites

### Required Accounts & Services
- ✅ **WordPress Website** (5.0+ with PHP 7.4+)
- ✅ **n8n Account** (Cloud or Self-hosted)
- ✅ **Telegram Bot** (Free)
- ✅ **AI Service Account** (Choose one):
  - OpenAI API Key
  - Claude API Key  
  - Gemini API Key

### System Requirements
- WordPress 5.0 or higher
- PHP 7.4 or higher
- cURL extension enabled
- Admin access to WordPress site

---

## 2. WordPress Plugin Installation

### Step 1: Download the Plugin
1. Go to your GitHub repository
2. Download the `telegram-blog-publisher` folder
3. Or clone the repository:
   ```bash
   git clone https://github.com/your-username/your-repo.git
   ```

### Step 2: Upload to WordPress
1. **Via WordPress Admin:**
   - Go to `Plugins → Add New → Upload Plugin`
   - Choose the `telegram-blog-publisher.zip` file
   - Click "Install Now"

2. **Via FTP/SFTP:**
   - Upload the `telegram-blog-publisher` folder to `/wp-content/plugins/`
   - Ensure proper file permissions (755 for folders, 644 for files)

### Step 3: Activate the Plugin
1. Go to `Plugins → Installed Plugins`
2. Find "Telegram Blog Publisher"
3. Click "Activate"

---

## 3. Plugin Configuration

### Step 1: Access Plugin Settings
1. In WordPress Admin, go to **"Telegram Publisher"** in the sidebar
2. You'll see the main dashboard

### Step 2: Configure AI Service
1. Go to **"Settings"** tab
2. Choose your AI service:
   - **OpenAI (Recommended)**: Fast and cost-effective
   - **Claude**: Great for detailed content
   - **Gemini**: Free tier available

3. **Get API Key:**
   - **OpenAI**: Go to [platform.openai.com/api-keys](https://platform.openai.com/api-keys)
   - **Claude**: Go to [console.anthropic.com](https://console.anthropic.com/)
   - **Gemini**: Go to [makersuite.google.com/app/apikey](https://makersuite.google.com/app/apikey)

4. **Enter API Key** in the plugin settings
5. **Set Default Author** (choose from existing users)
6. **Set Default Category** (optional)
7. **Choose Auto-Publish** (draft or publish immediately)

### Step 3: Get Webhook Information
1. In the **Dashboard** tab, you'll see:
   - **Webhook URL**: `https://yoursite.com/wp-json/telegram-blog-publisher/v1/webhook`
   - **Webhook Secret**: A randomly generated secret key
2. **Copy both values** - you'll need them for n8n

### Step 4: Test the Plugin
1. Click **"Test Webhook"** button
2. If successful, you'll see a test post created
3. Check that the post appears in your WordPress posts

---

## 4. n8n Workflow Setup

### Step 1: Create New Workflow
1. Open n8n (cloud or self-hosted)
2. Click **"New Workflow"**
3. Name it "Telegram to WordPress Blog"

### Step 2: Add Telegram Trigger
1. **Add Node**: Search for "Telegram Trigger"
2. **Configure Telegram Bot**:
   - Get bot token from [@BotFather](https://t.me/botfather)
   - Enter bot token in the node
   - Test connection

### Step 3: Add Data Processing Node
1. **Add Node**: Search for "Set" or "Code"
2. **Map Telegram Data**:
   ```javascript
   // Extract message text
   const messageText = $input.first().json.message.text;
   
   // Split into topic and details (if formatted)
   let topic, details;
   
   if (messageText.includes('Topic:') && messageText.includes('Details:')) {
     const parts = messageText.split('Details:');
     topic = parts[0].replace('Topic:', '').trim();
     details = parts[1].trim();
   } else {
     topic = messageText;
     details = messageText;
   }
   
   // Return structured data
   return {
     topic: topic,
     details: details,
     category: "General",
     tags: "telegram, auto-generated",
     status: "draft"
   };
   ```

### Step 4: Add HTTP Request Node
1. **Add Node**: Search for "HTTP Request"
2. **Configure Request**:
   - **Method**: POST
   - **URL**: `https://yoursite.com/wp-json/telegram-blog-publisher/v1/webhook`
   - **Headers**:
     ```
     Content-Type: application/json
     X-Webhook-Secret: your_webhook_secret_here
     ```
   - **Body**: Use the output from the previous node

### Step 5: Add Response Node
1. **Add Node**: Search for "Respond to Webhook"
2. **Configure Response**:
   - **Response Code**: 200
   - **Response Body**: 
     ```json
     {
       "success": true,
       "message": "Blog post created successfully"
     }
     ```

### Step 6: Connect All Nodes
1. **Telegram Trigger** → **Set/Code** → **HTTP Request** → **Respond to Webhook**
2. **Save the workflow**
3. **Activate the workflow**

---

## 5. Telegram Bot Setup

### Step 1: Create Telegram Bot
1. Open Telegram and search for [@BotFather](https://t.me/botfather)
2. Send `/newbot`
3. Choose a name: `Your Blog Publisher Bot`
4. Choose a username: `your_blog_publisher_bot`
5. **Save the bot token** - you'll need it for n8n

### Step 2: Configure Bot Commands (Optional)
1. Send `/setcommands` to @BotFather
2. Select your bot
3. Send:
   ```
   start - Start the bot
   help - Get help
   blog - Create a new blog post
   ```

### Step 3: Test Bot Connection
1. Search for your bot in Telegram
2. Send `/start`
3. Send a test message: "Write about the benefits of remote work"

---

## 6. Testing the Complete Workflow

### Step 1: Test n8n Workflow
1. In n8n, click **"Execute Workflow"**
2. Send a test message to your Telegram bot
3. Check the execution log in n8n
4. Verify the HTTP request was sent successfully

### Step 2: Test WordPress Integration
1. Send a message to your Telegram bot:
   ```
   Topic: Benefits of Meditation
   Details: Write about how meditation improves mental health, reduces stress, and enhances focus. Include practical tips for beginners.
   ```
2. Check your WordPress admin for the new post
3. Verify the post was created with proper content

### Step 3: Test Different Message Formats
1. **Simple format**: "Write about sustainable living"
2. **Detailed format**: 
   ```
   Topic: Sustainable Living Tips
   Details: Share practical advice for reducing environmental impact, including energy saving, waste reduction, and eco-friendly products.
   ```

---

## 7. Troubleshooting

### Common Issues & Solutions

#### ❌ "Webhook test failed"
**Solution:**
- Check if the webhook URL is correct
- Verify the webhook secret matches
- Ensure the plugin is activated
- Check WordPress REST API is enabled

#### ❌ "AI content generation failed"
**Solution:**
- Verify your API key is correct
- Check if you have credits/quota remaining
- Try a different AI service
- Check your internet connection

#### ❌ "n8n workflow not triggering"
**Solution:**
- Verify the Telegram bot token is correct
- Check if the workflow is activated
- Ensure the bot is added to your chat
- Check n8n execution logs

#### ❌ "WordPress post not created"
**Solution:**
- Check n8n HTTP request logs
- Verify the webhook endpoint is accessible
- Check WordPress error logs
- Ensure proper user permissions

### Debug Steps
1. **Check n8n Execution Logs**
2. **Check WordPress Error Logs**
3. **Test webhook manually** using curl:
   ```bash
   curl -X POST https://yoursite.com/wp-json/telegram-blog-publisher/v1/webhook \
   -H "Content-Type: application/json" \
   -H "X-Webhook-Secret: your_secret" \
   -d '{"topic":"Test","details":"Test content"}'
   ```

---

## 8. Advanced Configuration

### Custom Message Formats
You can customize how n8n processes different message formats:

```javascript
// Advanced message processing
const messageText = $input.first().json.message.text;

// Check for specific commands
if (messageText.startsWith('/blog')) {
  const content = messageText.replace('/blog', '').trim();
  return {
    topic: content,
    details: content,
    category: "Blog",
    tags: "telegram, command",
    status: "publish"
  };
}

// Check for structured format
if (messageText.includes('|')) {
  const parts = messageText.split('|');
  return {
    topic: parts[0].trim(),
    details: parts[1].trim(),
    category: parts[2]?.trim() || "General",
    tags: parts[3]?.trim() || "telegram",
    status: "draft"
  };
}

// Default processing
return {
  topic: messageText,
  details: messageText,
  category: "General",
  tags: "telegram",
  status: "draft"
};
```

### Multiple Categories
Set up different categories based on keywords:

```javascript
const messageText = $input.first().json.message.text.toLowerCase();
let category = "General";

if (messageText.includes('tech') || messageText.includes('programming')) {
  category = "Technology";
} else if (messageText.includes('health') || messageText.includes('fitness')) {
  category = "Health";
} else if (messageText.includes('business') || messageText.includes('marketing')) {
  category = "Business";
}

return {
  topic: $input.first().json.message.text,
  details: $input.first().json.message.text,
  category: category,
  tags: "telegram, " + category.toLowerCase(),
  status: "draft"
};
```

### Auto-Publishing Rules
Configure different publishing rules:

```javascript
const messageText = $input.first().json.message.text;
let status = "draft";

// Auto-publish if message contains specific keywords
if (messageText.includes('publish') || messageText.includes('urgent')) {
  status = "publish";
}

// Auto-publish if from specific users
const userId = $input.first().json.message.from.id;
const trustedUsers = [123456789, 987654321]; // Add trusted user IDs
if (trustedUsers.includes(userId)) {
  status = "publish";
}

return {
  topic: messageText,
  details: messageText,
  category: "General",
  tags: "telegram",
  status: status
};
```

---

## 🎬 Video Content Ideas

### Tutorial Series
1. **"Complete Setup Guide"** - Walk through entire process
2. **"n8n Workflow Creation"** - Focus on n8n configuration
3. **"Advanced Customization"** - Show advanced features
4. **"Troubleshooting Common Issues"** - Fix common problems

### Demo Videos
1. **"Publish Blog in 30 Seconds"** - Quick demo
2. **"AI-Powered Content Creation"** - Show AI capabilities
3. **"Multiple Message Formats"** - Different input methods
4. **"Real-time Publishing"** - Live demonstration

### Comparison Videos
1. **"Manual vs Automated Blogging"** - Time comparison
2. **"Different AI Services"** - Compare OpenAI, Claude, Gemini
3. **"Before vs After Setup"** - Show transformation

---

## 📊 Success Metrics

### Track These Metrics
- **Posts Generated**: Count of successful blog posts
- **Time Saved**: Compare manual vs automated time
- **AI Quality**: Rate of content quality
- **User Engagement**: Comments, shares, views
- **Error Rate**: Failed webhook requests

### Monitoring Dashboard
The plugin includes a built-in dashboard showing:
- Total posts generated
- Recent activity
- Success/failure rates
- Webhook performance

---

## 🔒 Security Best Practices

### Webhook Security
- Keep webhook secret secure
- Use HTTPS for all communications
- Regularly rotate webhook secrets
- Monitor webhook access logs

### API Key Security
- Store API keys securely
- Use environment variables when possible
- Regularly rotate API keys
- Monitor API usage and costs

### WordPress Security
- Keep WordPress and plugins updated
- Use strong passwords
- Enable two-factor authentication
- Regular security scans

---

## 🚀 Next Steps

### After Setup
1. **Test thoroughly** with different message types
2. **Train your team** on the new workflow
3. **Monitor performance** and optimize
4. **Create content calendar** for regular posting
5. **Analyze results** and improve

### Scaling Up
1. **Multiple bots** for different content types
2. **Advanced n8n workflows** with conditions
3. **Custom AI prompts** for specific industries
4. **Integration with other tools** (social media, email)

---

## 📞 Support

### Getting Help
- **Plugin Issues**: Check WordPress error logs
- **n8n Issues**: Check n8n execution logs
- **AI Issues**: Verify API keys and quotas
- **General Support**: Contact vikram@barcodemine.com

### Community
- **GitHub Issues**: Report bugs and request features
- **Documentation**: Check README.md for updates
- **Video Tutorials**: Follow our YouTube channel

---

**🎉 Congratulations! You now have a complete AI-powered blog publishing system that works from Telegram to WordPress!**
