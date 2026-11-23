# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import os
import logging
from datetime import datetime
import asyncio
import json
from sqlalchemy import create_engine, Column, Integer, String, DateTime, Text, ForeignKey, Boolean, func
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker, relationship
from sqlalchemy.ext.asyncio import create_async_engine, AsyncSession
from sqlalchemy.future import select
import aiosqlite


logger = logging.getLogger(__name__)

Base = declarative_base()

class User(Base):
    __tablename__ = 'users'
    
    id = Column(Integer, primary_key=True)
    user_id = Column(Integer, unique=True)
    username = Column(String(50))
    first_name = Column(String(100))
    settings = Column(Text)  
    notifications_enabled = Column(Boolean, default=True)
    created_at = Column(DateTime, default=datetime.now)
    last_active = Column(DateTime, default=datetime.now)
    total_whispers_sent = Column(Integer, default=0)
    total_whispers_received = Column(Integer, default=0)
    whispers_sent = relationship("Whisper", back_populates="sender", foreign_keys="Whisper.from_user_id")
    whispers_received = relationship("Whisper", back_populates="recipient", foreign_keys="Whisper.to_user_id")

class Whisper(Base):
    __tablename__ = 'whispers'
    
    id = Column(Integer, primary_key=True)
    whisper_id = Column(String(50), unique=True, index=True)
    from_user_id = Column(Integer, ForeignKey('users.user_id'))
    to_user_id = Column(Integer, ForeignKey('users.user_id'), nullable=True)
    to_username = Column(String(50))  
    message = Column(Text)
    theme = Column(String(20), default='default')
    created_at = Column(DateTime, default=datetime.now)
    read_at = Column(DateTime, nullable=True)
    deleted_at = Column(DateTime, nullable=True)
    expires_at = Column(DateTime, nullable=True)  
    is_encrypted = Column(Boolean, default=False)
    
    sender = relationship("User", back_populates="whispers_sent", foreign_keys=[from_user_id])
    recipient = relationship("User", back_populates="whispers_received", foreign_keys=[to_user_id])

class Database:
    def __init__(self):
        self.engine = create_async_engine('sqlite+aiosqlite:///whisper_bot.db')
        self.async_session = sessionmaker(
            self.engine, class_=AsyncSession, expire_on_commit=False
        )

    async def init_db(self):
        """Initialize database tables"""
        try:
            logger.info("Creating database tables...")
            async with self.engine.begin() as conn:
                await conn.run_sync(Base.metadata.create_all)
            logger.info("Database tables created successfully")
        except Exception as e:
            logger.error(f"Error creating database tables: {e}")
            raise

    async def get_or_create_user(self, user_id: int, username: str = None, first_name: str = None):
        """Get or create a user"""
        try:
            async with self.async_session() as session:
                stmt = select(User).where(User.user_id == user_id)
                result = await session.execute(stmt)
                user = result.scalar_one_or_none()
                
                if not user:
                    logger.info(f"Creating new user: {user_id}")
                    user = User(
                        user_id=user_id,
                        username=username,
                        first_name=first_name,
                        settings=json.dumps({
                            'theme': 'default',
                            'notifications': True,
                            'auto_delete': False,
                            'default_timer': None
                        })
                    )
                    session.add(user)
                    await session.commit()
                elif username and username != user.username:
                    logger.info(f"Updating username for user {user_id}")
                    user.username = username
                    user.last_active = datetime.now()
                    await session.commit()
                
                return user
        except Exception as e:
            logger.error(f"Error in get_or_create_user: {e}")
            raise

    async def save_whisper(self, from_user_id: int, to_username: str, message: str, whisper_id: str, theme: str = 'default', expires_in: int = None):
        """Save a new whisper"""
        try:
            async with self.async_session() as session:

                stmt = select(User).where(User.user_id == from_user_id)
                result = await session.execute(stmt)
                sender = result.scalar_one_or_none()
                
                if not sender:
                    logger.info(f"Creating new user for sender: {from_user_id}")
                    sender = User(user_id=from_user_id)
                    session.add(sender)
                

                logger.info(f"Creating whisper with ID: {whisper_id}")
                whisper = Whisper(
                    whisper_id=whisper_id,
                    from_user_id=from_user_id,
                    to_username=to_username,
                    message=message,
                    theme=theme,
                    created_at=datetime.now(),
                    expires_at=datetime.now().timestamp() + expires_in if expires_in else None
                )
                

                sender.total_whispers_sent += 1
                sender.last_active = datetime.now()
                
                session.add(whisper)
                await session.commit()
                logger.info(f"Whisper {whisper_id} saved successfully")
                return whisper
        except Exception as e:
            logger.error(f"Error in save_whisper: {e}")
            raise

    async def get_whisper(self, whisper_id: str):
        """Get a whisper by ID"""
        try:
            async with self.async_session() as session:
                logger.info(f"Fetching whisper: {whisper_id}")
                stmt = select(Whisper).where(
                    Whisper.whisper_id == whisper_id,
                    Whisper.deleted_at == None
                )
                result = await session.execute(stmt)
                whisper = result.scalar_one_or_none()
                
                if whisper:
  
                    if whisper.expires_at and datetime.now().timestamp() > whisper.expires_at:
                        logger.info(f"Whisper {whisper_id} has expired")
                        whisper.deleted_at = datetime.now()
                        await session.commit()
                        return None
                    
                    logger.info(f"Whisper {whisper_id} found")
                    return {
                        'id': whisper.whisper_id,
                        'from_user_id': whisper.from_user_id,
                        'to_username': whisper.to_username,
                        'message': whisper.message,
                        'created_at': whisper.created_at,
                        'read_at': whisper.read_at,
                        'theme': whisper.theme
                    }
                logger.info(f"Whisper {whisper_id} not found")
                return None
        except Exception as e:
            logger.error(f"Error in get_whisper: {e}")
            raise

    async def mark_whisper_as_read(self, whisper_id: str, reader_id: int):
        """Mark a whisper as read"""
        async with self.async_session() as session:
            stmt = select(Whisper).where(Whisper.whisper_id == whisper_id)
            result = await session.execute(stmt)
            whisper = result.scalar_one_or_none()
            
            if whisper and not whisper.read_at:
                whisper.read_at = datetime.now()
                whisper.to_user_id = reader_id
                

                stmt = select(User).where(User.user_id == reader_id)
                result = await session.execute(stmt)
                recipient = result.scalar_one_or_none()
                
                if recipient:
                    recipient.total_whispers_received += 1
                    recipient.last_active = datetime.now()
                
                await session.commit()
                return True
            return False

    async def get_user_unread_whispers(self, username: str):
        """Get unread whispers for a user"""
        async with self.async_session() as session:
            stmt = select(Whisper).where(
                Whisper.to_username == username,
                Whisper.read_at == None,
                Whisper.deleted_at == None
            )
            result = await session.execute(stmt)
            whispers = result.scalars().all()
            return [
                {
                    'id': w.whisper_id,
                    'from_user_id': w.from_user_id,
                    'message': w.message,
                    'created_at': w.created_at,
                    'theme': w.theme
                }
                for w in whispers
            ]

    async def get_user_stats(self, user_id: int):
        """Get user statistics"""
        async with self.async_session() as session:
            stmt = select(User).where(User.user_id == user_id)
            result = await session.execute(stmt)
            user = result.scalar_one_or_none()
            
            if user:

                unread_count = await session.execute(
                    select(func.count()).where(
                        Whisper.to_user_id == user_id,
                        Whisper.read_at == None,
                        Whisper.deleted_at == None
                    )
                )
                
                return {
                    'total_sent': user.total_whispers_sent,
                    'total_received': user.total_whispers_received,
                    'unread_count': unread_count.scalar(),
                    'member_since': user.created_at,
                    'last_active': user.last_active
                }
            return None

    async def toggle_notifications(self, user_id: int, enabled: bool):
        """Toggle user notifications"""
        async with self.async_session() as session:
            stmt = select(User).where(User.user_id == user_id)
            result = await session.execute(stmt)
            user = result.scalar_one_or_none()
            
            if user:
                user.notifications_enabled = enabled
                user.last_active = datetime.now()
                

                settings = json.loads(user.settings)
                settings['notifications'] = enabled
                user.settings = json.dumps(settings)
                
                await session.commit()
                return True
            return False

    async def update_user_settings(self, user_id: int, settings: str):
        """Update user settings"""
        async with self.async_session() as session:
            stmt = select(User).where(User.user_id == user_id)
            result = await session.execute(stmt)
            user = result.scalar_one_or_none()
            
            if not user:
                user = User(
                    user_id=user_id,
                    settings=settings,
                    created_at=datetime.now()
                )
                session.add(user)
            else:
                user.settings = settings
                user.last_active = datetime.now()
            
            await session.commit()

    async def get_user_settings(self, user_id: int):
        """Get user settings"""
        async with self.async_session() as session:
            stmt = select(User).where(User.user_id == user_id)
            result = await session.execute(stmt)
            user = result.scalar_one_or_none()
            
            if not user:

                default_settings = json.dumps({
                    'theme': 'default',
                    'notifications': True,
                    'auto_delete': False,
                    'default_timer': None
                })
                await self.update_user_settings(user_id, default_settings)
                return default_settings
            
            return user.settings

    async def get_user_whisper_history(self, user_id: int, limit: int = 10):
        """Get user's whisper history"""
        async with self.async_session() as session:

            sent_stmt = select(Whisper).where(
                Whisper.from_user_id == user_id,
                Whisper.deleted_at == None
            ).order_by(Whisper.created_at.desc()).limit(limit)
            

            received_stmt = select(Whisper).where(
                Whisper.to_user_id == user_id,
                Whisper.deleted_at == None
            ).order_by(Whisper.created_at.desc()).limit(limit)
            
            sent_result = await session.execute(sent_stmt)
            received_result = await session.execute(received_stmt)
            
            sent_whispers = sent_result.scalars().all()
            received_whispers = received_result.scalars().all()
            
            return {
                'sent': [
                    {
                        'id': w.whisper_id,
                        'to_username': w.to_username,
                        'message': w.message,
                        'created_at': w.created_at,
                        'read_at': w.read_at,
                        'theme': w.theme
                    }
                    for w in sent_whispers
                ],
                'received': [
                    {
                        'id': w.whisper_id,
                        'from_user_id': w.from_user_id,
                        'message': w.message,
                        'created_at': w.created_at,
                        'theme': w.theme
                    }
                    for w in received_whispers
                ]
            }

    async def delete_expired_whispers(self):
        """Delete whispers that have expired"""
        async with self.async_session() as session:
            current_time = datetime.now().timestamp()
            stmt = select(Whisper).where(
                Whisper.expires_at != None,
                Whisper.expires_at <= current_time,
                Whisper.deleted_at == None
            )
            result = await session.execute(stmt)
            expired_whispers = result.scalars().all()
            
            for whisper in expired_whispers:
                whisper.deleted_at = datetime.now()
            
            await session.commit()
            return len(expired_whispers) 