# AreaMusic  
**A PocketMine-MP Plugin**  

## 📌 Description  
**AreaMusic** is a **PocketMine-MP API 5** plugin that allows server owners to add background music to specific areas. The music will automatically play when a player enters the area and stop when they leave.  

## 🚀 Features  
✅ Automatically play music when a player enters a designated area.  
✅ Stop the music when the player leaves the area.  
✅ Supports multiple areas with different music.  
✅ Easy configuration using in-game commands.  
✅ No need to restart the server after adding a music area.

## 🎮 How to Use  
Use the following commands to set up a music area:  

| Command | Description |  
|---------|------------|  
| `/musicpos1` | Set **Pos1** of the music area. |  
| `/musicpos2` | Set **Pos2** of the music area. |  
| `/setmusicarea` | Save the music area from **Pos1** to **Pos2**. |  

**Example Usage:**  
1. Stand at one corner of the area and type:
`/musicpos1`

2. Stand at the opposite corner and type:
`/musicpos2`

3. Save the area with:
`/setmusicarea`

4. The music will start playing when a player enters the area.  

## ⚙ Configuration (`music_areas.yml`)  
This file stores the list of saved music areas.  

```yaml
music_areas:
- pos1:
   x: 100
   y: 65
   z: 200
 pos2:
   x: 120
   y: 75
   z: 220
```
❓ FAQ

❔ Will the music stop automatically when a player leaves the area?
✔ Yes, the music will stop automatically when the player exits the area.
❔ Can I set multiple music areas?
✔ Yes, you can define multiple areas with different music.

📜 License

This plugin is released under the MIT License.

💡 Credits

👨‍💻 Developer: VsrStudio
🌎 Website: vsrstudio.web.id
💌 Gmail: vsrstudio@xonefg.xyz
📂 GitHub Repository: VsrStudio/AreaMusic

Save this as `README.md` in your GitHub repository. Let me know if you need modifications!
