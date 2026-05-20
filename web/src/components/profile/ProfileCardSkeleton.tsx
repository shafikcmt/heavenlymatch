export function ProfileCardSkeleton() {
  return (
    <div className="flex flex-col rounded-2xl border border-slate-200 bg-white overflow-hidden animate-pulse">
      <div className="aspect-[3/4] bg-slate-200" />
      <div className="p-4 space-y-3">
        <div className="h-4 w-2/3 rounded bg-slate-200" />
        <div className="h-3 w-1/3 rounded bg-slate-200" />
        <div className="flex gap-2">
          <div className="h-5 w-16 rounded-full bg-slate-200" />
          <div className="h-5 w-20 rounded-full bg-slate-200" />
        </div>
        <div className="flex gap-2 pt-1">
          <div className="h-8 w-8 rounded-full bg-slate-200" />
          <div className="h-8 w-8 rounded-full bg-slate-200" />
          <div className="ml-auto h-8 w-16 rounded-lg bg-slate-200" />
        </div>
      </div>
    </div>
  );
}
