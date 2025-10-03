# 🚀 Quick Setup Script for Telegram Blog Publisher

## ⚡ 5-Minute Setup Guide

### Step 1: WordPress Plugin (2 minutes)
```bash
# Download and install the plugin
1. Go to WordPress Admin → Plugins → Add New → Upload Plugin
2. Upload telegram-blog-publisher.zip
3. Click "Install Now" → "Activate"
4. Go to "Telegram Publisher" in admin menu
```

### Step 2: Configure AI Service (1 minute)
```
1. Choose AI Service: OpenAI (recommended)
2. Get API Key: https://platform.openai.com/api-keys
3. Enter API Key in plugin settings
4. Click "Save Settings"
```

### Step 3: Get Webhook Info (30 seconds)
```
1. Copy Webhook URL from dashboard
2. Copy Webhook Secret from dashboard
3. Test webhook with "Test Webhook" button
```

### Step 4: n8n Workflow (2 minutes)
```
1. Open n8n → New Workflow
2. Add "Telegram Trigger" node
3. Add "Set" node with this code:
   ```javascript
   return {
     topic: $input.first().json.message.text,
     details: $input.first().json.message.text,
     category: "General",
     tags: "telegram",
     status: "draft"
   };
   ```
4. Add "HTTP Request" node:
   - Method: POST
   - URL: [your webhook URL]
   - Headers: X-Webhook-Secret: [your secret]
   - Body: JSON from previous node
5. Connect nodes and activate workflow
```

### Step 5: Test (30 seconds)
```
1. Send message to your Telegram bot
2. Check WordPress for new post
3. Success! 🎉
```

## 🔧 n8n Workflow JSON Template

Copy this JSON into n8n to create the workflow instantly:

```json
{
  "name": "Telegram to WordPress Blog",
  "nodes": [
    {
      "parameters": {
        "updates": [
          "message"
        ]
      },
      "id": "telegram-trigger",
      "name": "Telegram Trigger",
      "type": "n8n-nodes-base.telegramTrigger",
      "typeVersion": 1,
      "position": [240, 300]
    },
    {
      "parameters": {
        "values": {
          "string": [
            {
              "name": "topic",
              "value": "={{ $json.message.text }}"
            },
            {
              "name": "details", 
              "value": "={{ $json.message.text }}"
            },
            {
              "name": "category",
              "value": "General"
            },
            {
              "name": "tags",
              "value": "telegram, auto-generated"
            },
            {
              "name": "status",
              "value": "draft"
            }
          ]
        }
      },
      "id": "set-data",
      "name": "Set Data",
      "type": "n8n-nodes-base.set",
      "typeVersion": 1,
      "position": [460, 300]
    },
    {
      "parameters": {
        "url": "https://yoursite.com/wp-json/telegram-blog-publisher/v1/webhook",
        "options": {
          "headers": {
            "Content-Type": "application/json",
            "X-Webhook-Secret": "your_webhook_secret_here"
          }
        },
        "sendBody": true,
        "bodyParameters": {
          "parameters": [
            {
              "name": "topic",
              "value": "={{ $json.topic }}"
            },
            {
              "name": "details",
              "value": "={{ $json.details }}"
            },
            {
              "name": "category", 
              "value": "={{ $json.category }}"
            },
            {
              "name": "tags",
              "value": "={{ $json.tags }}"
            },
            {
              "name": "status",
              "value": "={{ $json.status }}"
            }
          ]
        }
      },
      "id": "http-request",
      "name": "HTTP Request",
      "type": "n8n-nodes-base.httpRequest",
      "typeVersion": 1,
      "position": [680, 300]
    },
    {
      "parameters": {
        "respondWith": "json",
        "responseBody": "={{ { \"success\": true, \"message\": \"Blog post created successfully\" } }}"
      },
      "id": "respond",
      "name": "Respond to Webhook",
      "type": "n8n-nodes-base.respondToWebhook",
      "typeVersion": 1,
      "position": [900, 300]
    }
  ],
  "connections": {
    "Telegram Trigger": {
      "main": [
        [
          {
            "node": "Set Data",
            "type": "main",
            "index": 0
          }
        ]
      ]
    },
    "Set Data": {
      "main": [
        [
          {
            "node": "HTTP Request",
            "type": "main",
            "index": 0
          }
        ]
      ]
    },
    "HTTP Request": {
      "main": [
        [
          {
            "node": "Respond to Webhook",
            "type": "main",
            "index": 0
          }
        ]
      ]
    }
  }
}
```

## 🎯 One-Click Test Messages

### Test Message 1: Simple
```
Write about the benefits of meditation
```

### Test Message 2: Detailed
```
Topic: Remote Work Best Practices
Details: Share practical tips for staying productive while working from home, including time management, communication strategies, and maintaining work-life balance.
```

### Test Message 3: With Keywords
```
Write about sustainable living tips for beginners
```

## 🔍 Troubleshooting Quick Fixes

### ❌ "Webhook test failed"
```bash
# Check these:
1. Plugin is activated
2. Webhook URL is correct
3. Webhook secret matches
4. WordPress REST API is enabled
```

### ❌ "AI content generation failed"
```bash
# Check these:
1. API key is correct
2. You have credits/quota
3. Internet connection is working
4. Try different AI service
```

### ❌ "n8n workflow not working"
```bash
# Check these:
1. Bot token is correct
2. Workflow is activated
3. All nodes are connected
4. Check execution logs
```

## 📊 Success Indicators

### ✅ Everything Working
- Webhook test shows "Success"
- Telegram bot responds to messages
- WordPress posts are created
- AI content is generated
- n8n shows successful executions

### 📈 Performance Metrics
- Webhook response time < 5 seconds
- AI content generation < 30 seconds
- Post creation < 10 seconds
- Overall workflow < 1 minute

## 🎬 Video Script: "5-Minute Setup"

```
00:00 - "Today I'll show you how to set up AI-powered blog publishing from Telegram in just 5 minutes!"

00:30 - "First, let's install the WordPress plugin..."

01:00 - "Now we'll configure the AI service..."

02:00 - "Next, we'll create the n8n workflow..."

03:30 - "Finally, let's test the complete system..."

04:30 - "And that's it! You now have automated blog publishing from Telegram!"

05:00 - "Don't forget to like and subscribe for more automation tips!"
```

---

**🚀 Ready to set up your Telegram Blog Publisher in 5 minutes!**
