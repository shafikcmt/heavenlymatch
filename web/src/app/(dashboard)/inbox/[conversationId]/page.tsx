"use client";

import { useState, useRef, useEffect } from "react";
import Link from "next/link";
import { Send, ChevronLeft, MoreVertical, Phone } from "lucide-react";

const MOCK_MESSAGES = [
  { id: "m1", senderId: "HM000201", text: "Assalamu Alaikum! I came across your profile and I believe we may be a good match.", sentAt: new Date(Date.now() - 3600000 * 2).toISOString() },
  { id: "m2", senderId: "ME",        text: "Walaikum Assalam! Thank you for reaching out. I reviewed your profile and would be happy to learn more about you.", sentAt: new Date(Date.now() - 3600000).toISOString() },
  { id: "m3", senderId: "HM000201", text: "JazakAllah. Could you tell me a little about your family background?", sentAt: new Date(Date.now() - 1800000).toISOString() },
];

function timeLabel(iso: string) {
  return new Date(iso).toLocaleTimeString("en-GB", { hour: "2-digit", minute: "2-digit" });
}

export default function ConversationPage() {
  const [messages, setMessages] = useState(MOCK_MESSAGES);
  const [input, setInput] = useState("");
  const bottomRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    bottomRef.current?.scrollIntoView({ behavior: "smooth" });
  }, [messages]);

  const send = () => {
    const text = input.trim();
    if (!text) return;
    setMessages((prev) => [
      ...prev,
      { id: `m${Date.now()}`, senderId: "ME", text, sentAt: new Date().toISOString() },
    ]);
    setInput("");
  };

  return (
    <div className="flex flex-col h-[calc(100vh-8rem)] max-w-2xl mx-auto">
      {/* Header */}
      <div className="flex items-center gap-3 border-b border-slate-200 bg-white px-4 py-3 rounded-t-2xl">
        <Link href="/inbox" className="text-slate-400 hover:text-slate-600">
          <ChevronLeft size={20} />
        </Link>
        <div className="h-9 w-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold">
          N
        </div>
        <div className="flex-1 min-w-0">
          <p className="font-semibold text-slate-900 text-sm">Nusrat Jahan</p>
          <p className="text-xs text-emerald-500">Active now</p>
        </div>
        <div className="flex gap-2 text-slate-400">
          <button className="hover:text-slate-600"><Phone size={18} /></button>
          <button className="hover:text-slate-600"><MoreVertical size={18} /></button>
        </div>
      </div>

      {/* Messages */}
      <div className="flex-1 overflow-y-auto p-4 space-y-3 bg-slate-50">
        {messages.map((msg) => {
          const isMe = msg.senderId === "ME";
          return (
            <div key={msg.id} className={`flex ${isMe ? "justify-end" : "justify-start"}`}>
              <div className={`max-w-[75%] group relative`}>
                <div className={`rounded-2xl px-4 py-2.5 text-sm ${
                  isMe
                    ? "bg-blue-600 text-white rounded-br-sm"
                    : "bg-white border border-slate-200 text-slate-800 rounded-bl-sm shadow-sm"
                }`}>
                  {msg.text}
                </div>
                <p className={`mt-1 text-[10px] text-slate-400 ${isMe ? "text-right" : "text-left"}`}>
                  {timeLabel(msg.sentAt)}
                </p>
              </div>
            </div>
          );
        })}
        <div ref={bottomRef} />
      </div>

      {/* Input */}
      <div className="border-t border-slate-200 bg-white p-3 rounded-b-2xl">
        <div className="flex items-center gap-2">
          <input
            value={input}
            onChange={(e) => setInput(e.target.value)}
            onKeyDown={(e) => { if (e.key === "Enter" && !e.shiftKey) { e.preventDefault(); send(); } }}
            placeholder="Type a message…"
            className="flex-1 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none"
          />
          <button
            onClick={send}
            disabled={!input.trim()}
            className="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-40 transition-colors"
          >
            <Send size={16} />
          </button>
        </div>
        <p className="mt-1.5 text-center text-[10px] text-slate-400">
          Stay respectful · Do not share personal contact info early
        </p>
      </div>
    </div>
  );
}
