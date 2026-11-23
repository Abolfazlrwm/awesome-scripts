-- ==============================
-- 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
-- Use, edit and redistribute only with attribution to @Abolfazl_rwm.
-- ==============================
function settings(msg,chat)
if base:sismember(TD_ID..'Gp:'..chat,'Del:Caption') then
Capdel = '|✓|'
else
Capdel = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Warn:Caption') then
Capwarn = '|✓|'
else
Capwarn = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Mute:Caption') then
Capmute = '|✓|'
else
Capmute = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Kick:Caption') then
Capkick = '|✓|'
else
Capkick = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Ban:Caption') then
Capban = '|✓|'
else
Capban = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Silent:Caption') then
Capsi = '|✓|'
else
Capsi = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Del:Videomsg') then
V_notdel = '|✓|'
else
V_notdel = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Warn:Videomsg') then
V_notwarn = '|✓|'
else
V_notwarn = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Mute:Videomsg') then
V_notmute = '|✓|'
else
V_notmute = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Kick:Videomsg') then
V_notkick = '|✓|'
else
V_notkick = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Ban:Videomsg') then
V_notban = '|✓|'
else
V_notban = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Silent:Videomsg') then
V_notsi = '|✓|'
else
V_notsi = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Del:Sticker') then
Stdel = '|✓|'
else
Stdel = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Warn:Sticker') then
Stwarn = '|✓|'
else
Stwarn = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Mute:Sticker') then
Stmute = '|✓|'
else
Stmute = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Kick:Sticker') then
Stkick = '|✓|'
else
Stkick = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Ban:Sticker') then
Stban = '|✓|'
else
Stban = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Silent:Sticker') then
Stsi = '|✓|'
else
Stsi = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Del:Stickers') then
Stsdel = '|✓|'
else
Stsdel = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Warn:Stickers') then
Stswarn = '|✓|'
else
Stswarn = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Mute:Stickers') then
Stsmute = '|✓|'
else
Stsmute = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Kick:Stickers') then
Stskick = '|✓|'
else
Stskick = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Ban:Stickers') then
Stsban = '|✓|'
else
Stsban = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Silent:Stickers') then
Stssi = '|✓|'
else
Stssi = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Del:Contact') then
Condel = '|✓|'
else
Condel = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Warn:Contact') then
Conwarn = '|✓|'
else
Conwarn = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Mute:Contact') then
Conmute = '|✓|'
else
Conmute = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Kick:Contact') then
Conkick = '|✓|'
else
Conkick = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Ban:Contact') then
Conban = '|✓|'
else
Conban = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Silent:Contact') then
Consi = '|✓|'
else
Consi = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Del:Document') then
Docdel = '|✓|'
else
Docdel = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Warn:Document') then
Docwarn = '|✓|'
else
Docwarn = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Mute:Document') then
Docmute = '|✓|'
else
Docmute = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Kick:Document') then
Dockick = '|✓|'
else
Dockick = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Ban:Document') then
Docban = '|✓|'
else
Docban = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Silent:Document') then
Docsi = '|✓|'
else
Docsi = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Del:Voice') then
Voicedel = '|✓|'
else
Voicedel = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Warn:Voice') then
Voicewarn = '|✓|'
else
Voicewarn = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Mute:Voice') then
Voicemute = '|✓|'
else
Voicemute = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Kick:Voice') then
Voicekick = '|✓|'
else
Voicekick = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Ban:Voice') then
Voiceban = '|✓|'
else
Voiceban = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Silent:Voice') then
Voicesi = '|✓|'
else
Voicesi = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Del:Photo') then
Photodel = '|✓|'
else
Photodel = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Warn:Photo') then
Photowarn = '|✓|'
else
Photowarn = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Mute:Photo') then
Photomute = '|✓|'
else
Photomute = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Kick:Photo') then
Photokick = '|✓|'
else
Photokick = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Ban:Photo') then
Photoban = '|✓|'
else
Photoban = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Silent:Photo') then
Photosi = '|✓|'
else
Photosi = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Del:Game') then
Gamedel = '|✓|'
else
Gamedel = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Warn:Game') then
Gamewarn = '|✓|'
else
Gamewarn = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Mute:Game') then
Gamemute = '|✓|'
else
Gamemute = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Kick:Game') then
Gamekick = '|✓|'
else
Gamekick = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Ban:Game') then
Gameban = '|✓|'
else
Gameban = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Silent:Game') then
Gamesi = '|✓|'
else
Gamesi = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Del:Video') then
Videodel = '|✓|'
else
Videodel = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Warn:Video') then
Videowarn = '|✓|'
else
Videowarn = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Mute:Video') then
Videomute = '|✓|'
else
Videomute = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Kick:Video') then
Videokick = '|✓|'
else
Videokick = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Ban:Video') then
Videoban = '|✓|'
else
Videoban = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Silent:Video') then
Videosi = '|✓|'
else
Videosi = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Del:Audio') then
Musicdel = '|✓|'
else
Musicdel = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Warn:Audio') then
Musicwarn = '|✓|'
else
Musicwarn = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Mute:Audio') then
Musicmute = '|✓|'
else
Musicmute = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Kick:Audio') then
Musickick = '|✓|'
else
Musickick = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Ban:Audio') then
Musicban = '|✓|'
else
Musicban = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Silent:Audio') then
Musicsi = '|✓|'
else
Musicsi = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Del:Gif') then
Gifdel = '|✓|'
else
Gifdel = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Warn:Gif') then
Gifwarn = '|✓|'
else
Gifwarn = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Mute:Gif') then
Gifmute = '|✓|'
else
Gifmute = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Kick:Gif') then
Gifkick = '|✓|'
else
Gifkick = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Ban:Gif') then
Gifban = '|✓|'
else
Gifban = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Silent:Gif') then
Gifsi = '|✓|'
else
Gifsi = '|✗|'
end

if base:sismember(TD_ID..'Gp:'..chat,'Del:Edit') then
Editdel = '|✓|'
else
Editdel = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Warn:Edit') then
Editwarn = '|✓|'
else
Editwarn = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Mute:Edit') then
Editmute = '|✓|'
else
Editmute = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Kick:Edit') then
Editkick = '|✓|'
else
Editkick = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Ban:Edit') then
Editban = '|✓|'
else
Editban = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Silent:Edit') then
Editsi = '|✓|'
else
Editsi = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Del:Link') then
Linkdel = '|✓|'
else
Linkdel = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Warn:Link') then
Linkwarn = '|✓|'
else
Linkwarn = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Mute:Link') then
Linkmute = '|✓|'
else
Linkmute = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Kick:Link') then
Linkkick = '|✓|'
else
Linkkick = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Ban:Link') then
Linkban = '|✓|'
else
Linkban = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Silent:Link') then
Linksi = '|✓|'
else
Linksi = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Del:Username') then
Userdel = '|✓|'
else
Userdel = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Warn:Username') then
Userwarn = '|✓|'
else
Userwarn = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Mute:Username') then
Usermute = '|✓|'
else
Usermute = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Kick:Username') then
Userkick = '|✓|'
else
Userkick = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Ban:Username') then
Userban = '|✓|'
else
Userban = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Silent:Username') then
Usersi = '|✓|'
else
Usersi = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Del:Tag') then
Tagdel = '|✓|'
else
Tagdel = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Warn:Tag') then
Tagwarn = '|✓|'
else
Tagwarn = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Mute:Tag') then
Tagmute = '|✓|'
else
Tagmute = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Kick:Tag') then
Tagkick = '|✓|'
else
Tagkick = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Ban:Tag') then
Tagban = '|✓|'
else
Tagban = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Silent:Tag') then
Tagsi = '|✓|'
else
Tagsi = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Del:Inline') then
Inlinedel = '|✓|'
else
Inlinedel = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Warn:Inline') then
Inlinewarn = '|✓|'
else
Inlinewarn = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Mute:Inline') then
Inlinemute = '|✓|'
else
Inlinemute = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Kick:Inline') then
Inlinekick = '|✓|'
else
Inlinekick = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Ban:Inline') then
Inlineban = '|✓|'
else
Inlineban = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Silent:Inline') then
Inlinesi = '|✓|'
else
Inlinesi = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Del:Forward') then
Fwddel = '|✓|'
else
Fwddel = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Warn:Forward') then
Fwdwarn = '|✓|'
else
Fwdwarn = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Mute:Forward') then
Fwdmute = '|✓|'
else
Fwdmute = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Kick:Forward') then
Fwdkick = '|✓|'
else
Fwdkick = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Ban:Forward') then
Fwdban ='|✓|'
else
Fwdban ='|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Silent:Forward') then
Fwdsi = '|✓|'
else
Fwdsi = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Del:Bots') then
Botdel = '|✓|'
else
Botdel = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Warn:Bots') then
Botwarn = '|✓|'
else
Botwarn = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Mute:Bots') then
Botmute = '|✓|'
else
Botmute = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Kick:Bots') then
Botkick = '|✓|'
else
Botkick = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Ban:Bots') then
Botban = '|✓|'
else
Botban = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Silent:Bots') then
Botsi = '|✓|'
else
Botsi = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Del:Spam') then
Spamdel = '|✓|'
else
Spamdel = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Warn:Spam') then
Spamwarn = '|✓|'
else
Spamwarn = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Mute:Spam') then
Spammute = '|✓|'
else
Spammute = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Kick:Spam') then
Spamkick = '|✓|'
else
Spamkick = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Ban:Spam') then
Spamban = '|✓|'
else
Spamban = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Silent:Spam') then
Spamsi = '|✓|'
else
Spamsi = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Del:Persian') then
Fadel = '|✓|'
else
Fadel = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Warn:Persian') then
Fawarn = '|✓|'
else
Fawarn = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Mute:Persian') then
Famute = '|✓|'
else
Famute = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Kick:Persian') then
Fakick = '|✓|'
else
Fakick = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Ban:Persian') then
Faban = '|✓|'
else
Faban = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Silent:Persian') then
Fasi = '|✓|'
else
Fasi = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Del:English') then
Endel = '|✓|'
else
Endel = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Warn:English') then
Enwarn = '|✓|'
else
Enwarn = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Mute:English') then
Enmute = '|✓|'
else
Enmute = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Kick:English') then
Enkick = '|✓|'
else
Enkick = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Ban:English') then
Enban = '|✓|'
else
Enban = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Silent:English') then
Ensi = '|✓|'
else
Ensi = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Del:Text') then
Textdel = '|✓|'
else
Textdel = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Warn:Text') then
Textwarn = '|✓|'
else
Textwarn = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Mute:Text') then
Textmute = '|✓|'
else
Textmute = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Kick:Text') then
Textkick = '|✓|'
else
Textkick = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Ban:Text') then
Textban = '|✓|'
else
Textban = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Silent:Text') then
Textsi = '|✓|'
else
Textsi = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Del:Location') then
Locdel = '|✓|'
else
Locdel = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Warn:Location') then
Locwarn = '|✓|'
else
Locwarn = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Mute:Location') then
Locmute = '|✓|'
else
Locmute = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Kick:Location') then
Lockick = '|✓|'
else
Lockick = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Ban:Location') then
Locban = '|✓|'
else
Locban = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Silent:Location') then
Locsi = '|✓|'
else
Locsi = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Del:Flood') then
Flooddel = '|✓|'
else
Flooddel = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Warn:Flood') then
Floodwarn = '|✓|'
else
Floodwarn = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Mute:Flood') then
Floodmute = '|✓|'
else
Floodmute = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Kick:Flood') then
Floodkick = '|✓|'
else
Floodkick = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Ban:Flood') then
Floodban = '|✓|'
else
Floodban = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Silent:Flood') then
Floodsi = '|✓|'
else
Floodsi = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Del:Mention') then
Mentiondel = '|✓|'
else
Mentiondel = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Warn:Mention') then
Mentionwarn = '|✓|'
else
Mentionwarn = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Mute:Mention') then
Mentionmute = '|✓|'
else
Mentionmute = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Kick:Mention') then
Mentionkick = '|✓|'
else
Mentionkick = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Ban:Mention') then
Mentionban = '|✓|'
else
Mentionban = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Silent:Mention') then
Mentionsi = '|✓|'
else
Mentionsi = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Del:Hyper') then
Hyperdel = '|✓|'
else
Hyperdel = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Warn:Hyper') then
Hyperwarn = '|✓|'
else
Hyperwarn = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Mute:Hyper') then
Hypermute = '|✓|'
else
Hypermute = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Kick:Hyper') then
Hyperkick = '|✓|'
else
Hyperkick = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Ban:Hyper') then
Hyperban = '|✓|'
else
Hyperban = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Silent:Hyper') then
Hypersi = '|✓|'
else
Hypersi = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Del:Channelpost') then
Channelpostdel = '|✓|'
else
Channelpostdel = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Warn:Channelpost') then
Channelpostwarn = '|✓|'
else
Channelpostwarn = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Mute:Channelpost') then
Channelpostmute = '|✓|'
else
Channelpostmute = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Kick:Channelpost') then
Channelpostkick = '|✓|'
else
Channelpostkick = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Ban:Channelpost') then
Channelpostban = '|✓|'
else
Channelpostban = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Silent:Channelpost') then
Channelpostsi = '|✓|'
else
Channelpostsi = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Del:Biolink') then
Biolinkdel = '|✓|'
else
Biolinkdel = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Warn:Biolink') then
Biolinkwarn = '|✓|'
else
Biolinkwarn = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Mute:Biolink') then
Biolinkmute = '|✓|'
else
Biolinkmute = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Kick:Biolink') then
Biolinkkick = '|✓|'
else
Biolinkkick = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Ban:Biolink') then
Biolinkban = '|✓|'
else
Biolinkban = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Silent:Biolink') then
Biolinksi = '|✓|'
else
Biolinksi = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'Del:Malware') then
Malwaredel = '|✓|'
else
Malwaredel = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Warn:Malware') then
Malwarewarn = '|✓|'
else
Malwarewarn = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Mute:Malware') then
Malwaremute = '|✓|'
else
Malwaremute = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Kick:Malware') then
Malwarekick = '|✓|'
else
Malwarekick = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Ban:Malware') then
Malwareban = '|✓|'
else
Malwareban = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Silent:Malware') then
Malwaresi = '|✓|'
else
Malwaresi = '|✗|' 
end
if base:sismember(TD_ID..'Gp2:'..chat,'Welcomeon') then
welcome = '|✓|'
else
welcome = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Lock:Cmd') then
cmd = '|✓|'
else
cmd = '|✗|'
end
if base:sismember(TD_ID..'Gp:'..chat,'Lock:TGservice') then
tg = '|✓|'
else
tg = '|✗|' 
end
if base:sismember(TD_ID..'Gp2:'..chat,'Mute_All') then
mutealls = '|✓|'
else
mutealls = '|✗|' 
end
if base:sismember(TD_ID..'Gp2:'..chat,'automuteall') then
auto = '|✓|'
else
auto = '|✗|'
end
if base:sismember(TD_ID..'Gp2:'..chat,'PanelPv') then
pvpnl = '|✗|'
else
pvpnl = '|✓|' 
end
if base:sismember(TD_ID..'Gp2:'..chat,'kickbotpm') then
botpm = '|✓|'
else
botpm = '|✗|' 
end
if base:sismember(TD_ID..'Gp2:'..chat,'MsgCheckPm') then
lockpm = '|✓|'
else
lockpm = '|✗|' 
end
--Panel Sudo
if base:sismember(TD_ID..'PnlSudo:','add') and base:sismember(TD_ID..'PnlSudo:','rem') and base:sismember(TD_ID..'PnlSudo:','نصب') and base:sismember(TD_ID..'PnlSudo:','لغو نصب') then 
add = '✗'
else
add = '✓'
end
if base:sismember(TD_ID..'PnlSudo:','send groups') and base:sismember(TD_ID..'PnlSudo:','ارسال به گروها') then
send = '✗'
else
send = '✓'
end
if base:sismember(TD_ID..'PnlSudo:','fwd groups') and base:sismember(TD_ID..'PnlSudo:','فوروارد به گروها') then 
fwd = '✗'
else
fwd = '✓'
end
if base:sismember(TD_ID..'PnlSudo:','clean gbans') and base:sismember(TD_ID..'PnlSudo:','پاکسازی لیست گلوبال') then
gbans = '✗'
else
gbans = '✓'
end
if base:sismember(TD_ID..'PnlSudo:','joinchannel off') and base:sismember(TD_ID..'PnlSudo:','جوین چنل خاموش') and base:sismember(TD_ID..'PnlSudo:','joinchannel on') and base:sismember(TD_ID..'PnlSudo:','جوین چنل روشن') then 
joinch = '✗'
else
joinch = '✓'
end
if base:sismember(TD_ID..'PnlSudo:','kickall') and base:sismember(TD_ID..'PnlSudo:','اخراج همه') then
kickall = '✗'
else
kickall = '✓'
end
if base:sismember(TD_ID..'PnlSudo:','charge') and base:sismember(TD_ID..'PnlSudo:','شارژ') then
chargegp = '✗'
else
chargegp = '✓'
end
--Limit Cmd
if base:sismember(TD_ID..'LimitCmd:'..chat,'ban') and base:sismember(TD_ID..'LimitCmd:'..chat,'مسدود') then 
ban = '✗'
else
ban = '✓'
end
if base:sismember(TD_ID..'LimitCmd:'..chat,'mute') and base:sismember(TD_ID..'LimitCmd:'..chat,'سکوت') then 
mute = '✗'
else
mute = '✓'
end
if base:sismember(TD_ID..'LimitCmd:'..chat,'warn') and base:sismember(TD_ID..'LimitCmd:'..chat,'اخطار') then 
warn = '✗'
else
warn = '✓'
end
if base:sismember(TD_ID..'LimitCmd:'..chat,'muteall') and base:sismember(TD_ID..'LimitCmd:'..chat,'تعطیل کردن') and base:sismember(TD_ID..'LimitCmd:'..chat,'unmuteall') and base:sismember(TD_ID..'LimitCmd:'..chat,'بازکردن') then 
muteall = '✗'
else
muteall = '✓'
end
if base:sismember(TD_ID..'LimitCmd:'..chat,'cmgall') and base:sismember(TD_ID..'LimitCmd:'..chat,'پاکسازی همه') then 
cmg = '✗'
else
cmg = '✓'
end
if base:sismember(TD_ID..'LimitCmd:'..chat,'vip') and base:sismember(TD_ID..'LimitCmd:'..chat,'ویژه') then 
vip = '✗'
else
vip = '✓'
end
if base:sismember(TD_ID..'LimitCmd:'..chat,'lock') and base:sismember(TD_ID..'LimitCmd:'..chat,'del') and base:sismember(TD_ID..'LimitCmd:'..chat,'silent') and base:sismember(TD_ID..'LimitCmd:'..chat,'kick') and
base:sismember(TD_ID..'LimitCmd:'..chat,'unlock') and
base:sismember(TD_ID..'LimitCmd:'..chat,'ddel')and base:sismember(TD_ID..'LimitCmd:'..chat,'dwarn') and base:sismember(TD_ID..'LimitCmd:'..chat,'dmute') and base:sismember(TD_ID..'LimitCmd:'..chat,'dsilent') and base:sismember(TD_ID..'LimitCmd:'..chat,'dkick') and base:sismember(TD_ID..'LimitCmd:'..chat,'dban') and
base:sismember(TD_ID..'LimitCmd:'..chat,'قفل') and base:sismember(TD_ID..'LimitCmd:'..chat,'حذف')and base:sismember(TD_ID..'LimitCmd:'..chat,'سایلنت') and base:sismember(TD_ID..'LimitCmd:'..chat,'اخراج')then 
locks = '✗'
else
locks = '✓'
end

if base:sismember(TD_ID..'Gp:'..chat,'Lock:Join') then
joins = '|✓|'
else
joins = '|✗|' 
end
if base:sismember(TD_ID..'Gp:'..chat,'forceadd') then
if base:sismember(TD_ID..'Gp2:'..chat,'diamondlang') then
force = '#On'
else
force = '#فعال'
end
else
if base:sismember(TD_ID..'Gp2:'..chat,'diamondlang') then
force = '#OFF'
else
force = '#غیرفعال'
end
end
if base:sismember(TD_ID..'Gp:'..chat,'forcejoin') then
if base:sismember(TD_ID..'Gp2:'..chat,'diamondlang') then
join = '#On'
else
join = '#فعال'
end
else
if base:sismember(TD_ID..'Gp2:'..chat,'diamondlang') then
join = '#OFF'
else
join = '#غیرفعال'
end
end
local expire = base:ttl(TD_ID.."ExpireData:"..chat)
if expire == -1 then
if base:sismember(TD_ID..'Gp2:'..chat,'diamondlang') then
EXPIRE = 'Unlimited'
else
EXPIRE = 'نامحدود'
end
else
local d = math.floor(expire / day ) + 1
if base:sismember(TD_ID..'Gp2:'..chat,'diamondlang') then
EXPIRE = ""..d.." Day"
else
EXPIRE = ""..d.." روز"
end
end
end