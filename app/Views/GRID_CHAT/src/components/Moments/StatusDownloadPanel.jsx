import StatusAudiencePanel from "./StatusAudiencePanel";

const OPTIONS = [
  {
    value: "contacts",
    label: "My contacts",
    description: "Allow all of your contacts to download",
  },
  {
    value: "contacts_except",
    label: "My contacts except...",
    description: "Allow all except people you select",
  },
  {
    value: "only",
    label: "Only allow...",
    description: "Select specific contacts",
  },
];

const PICKER_TITLE = {
  contacts_except: {
    title: "Block downloads for...",
    subtitle: "These people will not get a download button on your statuses",
  },
  only: {
    title: "Allow downloads for...",
    subtitle: "Only these people will get a download button on your statuses",
  },
};

function StatusDownloadPanel(props) {
  return (
    <StatusAudiencePanel
      title="Status download"
      question="Who can download my statuses?"
      radioName="status-download"
      options={OPTIONS}
      pickerTitle={PICKER_TITLE}
      {...props}
    />
  );
}

export default StatusDownloadPanel;
